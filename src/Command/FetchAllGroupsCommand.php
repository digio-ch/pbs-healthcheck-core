<?php

namespace App\Command;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Statistics\GroupGeoLocation;
use App\Entity\Statistics\StatisticGroup;
use App\Exception\ApiException;
use App\Helper\BatchedRepository;
use App\Model\CommandStatistics;
use App\Model\LogMessage\SimpleLogMessage;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Repository\Statistics\GroupGeoLocationRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\GroupStructureAPIService;
use Digio\Logging\GelfLogger;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchAllGroupsCommand extends StatisticsCommand
{
    protected GroupStructureAPIService $apiService;

    protected SymfonyStyle $io;

    protected StatisticGroupRepository $statisticGroupRepository;

    protected GroupTypeRepository $groupTypeRepository;

    protected EntityManagerInterface $em;

    protected GroupRepository $groupRepository;

    protected GroupGeoLocationRepository $geoLocationRepository;

    protected GelfLogger $gelfLogger;

    protected $stats = [0, 0];

    /**
     * @param GroupStructureAPIService $apiService
     */
    public function __construct(
        EntityManagerInterface $em,
        GroupStructureAPIService $apiService,
        StatisticGroupRepository $statisticGroupRepository,
        GroupTypeRepository $groupTypeRepository,
        GroupRepository $groupRepository,
        GroupGeoLocationRepository $geoLocationRepository,
        GelfLogger $gelfLogger
    ) {
        $this->em = $em;
        $this->apiService = $apiService;
        $this->statisticGroupRepository = $statisticGroupRepository;
        $this->groupTypeRepository = $groupTypeRepository;
        $this->groupRepository = $groupRepository;
        $this->geoLocationRepository = $geoLocationRepository;
        $this->gelfLogger = $gelfLogger;
        parent::__construct();
    }


    public function configure()
    {
        $this->setName('app:fetch-all-groups')
            ->setDescription('Fetches all groups from MiData (' . $this->apiService->getUrl() . ') using the groups endpoint.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $start = microtime(true);
        $this->geoLocationRepository->deleteAll();
        $this->statisticGroupRepository->deleteAll();

        $batchedStatisticsRepository = new BatchedRepository($this->statisticGroupRepository);
        $batchedGeoRepository = new BatchedRepository($this->geoLocationRepository);
        $this->fetchGroupRecursive(2, null, null, $batchedStatisticsRepository, $batchedGeoRepository);
        $batchedStatisticsRepository->flush();
        $batchedGeoRepository->flush();

        //$this->fetchAllRemaining();
        $this->logMissingBranches();

        $this->stats[0] = microtime(true) - $start;
        return Command::SUCCESS;
    }

    private function logMissingBranches()
    {
        $groups = $this->statisticGroupRepository->findBy(['parent_group' => null]);
        foreach ($groups as $group) {
            if ($group->getId() === 2) {
                continue;
            }
            $this->gelfLogger->warning(new SimpleLogMessage($this->recursiveGetChildrenAsJsonString($group)));
            $this->io->warning('This branch is missing the parent: {' . $this->recursiveGetChildrenAsJsonString($group) . '}');
        }
    }

    /**
     * Return all children of a group as JSON like string
     */
    private function recursiveGetChildrenAsJsonString(StatisticGroup $group)
    {
        $result = '"' . $group->getId();
        $children = $group->getChildren();
        if (sizeof($children) === 0) {
            return $result . '": null,';
        }
        $result .= '": {';
        foreach ($children as $child) {
            $result .= $this->recursiveGetChildrenAsJsonString($child);
        }
        return $result . '},';
    }

    /**
     * Brutforces through all groups and fetches them. Takes longer because you have to wait for every 404 and you don't
     * know which index is the last. Don't use unless necessary
     */
    private function fetchAllRemaining()
    {
        for ($i = 2; $i < 11600; $i++) {
            $group = $this->statisticGroupRepository->findOneBy(['id' => $i]);
            if (is_null($group)) {
                $result = $this->fetchGroup($i);
                $content = $result->getContent()['groups'][0];
                $statisticGroup = new StatisticGroup();
                $rawGroup = $content;

                $name = trim($rawGroup['name']);
                $parentGroup = $rawGroup['links']['parent'] ?? null;
                if (!is_null($parentGroup)) {
                    $parentGroup = $this->statisticGroupRepository->findOneBy(['id' => $parentGroup]);
                }
                /** @var GroupType $groupType */
                $groupType = $this->groupTypeRepository->findOneBy(['deLabel' => $rawGroup['group_type']]);
                $statisticGroup->setId($i);
                $statisticGroup->setName($name);
                $statisticGroup->setParentGroup($parentGroup);
                $statisticGroup->setGroupType($groupType);
                $this->io->writeln('Adding ' . $i);
                $this->statisticGroupRepository->add($statisticGroup);
            }
        }
    }

    private function fetchGroup(int $id, bool $stopOnFail = false)
    {
        try {
            $result = $this->apiService->getGroup($id);
            if ($result->getStatusCode() !== 200) {
                $this->io->error([
                    'API call for group with id ' . $id . ' failed!',
                    'HTTP status code: ' . $result->getStatusCode()
                ]);
            }
            return $result;
        } catch (ClientException $e) {
            $this->io->error('Fetch for ' . $id . ' resultet in an error (' . $e->getCode() . ')');
            if ($stopOnFail) {
                $this->gelfLogger('Fetching Group (' . $id . ') failed with error code (' . $e->getCode() . '), stopped fetching.');
                throw new ApiException('Fetching Group (' . $id . ') failed, stopping fetching.');
            }
            return null;
        }
    }

    /**
     * Fetch a group and its children recursively
     */
    private function fetchGroupRecursive(int $id, ?StatisticGroup $parent, ?StatisticGroup $canton, BatchedRepository $batchedStatisticsRepository, BatchedRepository $batchedGeoRepository)
    {
        $result = $this->fetchGroup($id, true);
        $statisticGroup = new StatisticGroup();

        $rawGroup = $result->getContent()['groups'][0];
        $name = trim($rawGroup['name']);
        $children = $rawGroup['links']['children'] ?? [];
        /**
         * Sadly the group type we get from the regular group endpoint (statistic_group) is not the same one,
         * that we get from the group type endpoint (JSON file). So here we have to map the german label of a group type
         * to the group type key.
         * A side effect of this is that we can get Group types which just don't exist. (eg. Erziehungsberechtigter)
         * To prevent this from destroying this function we must ignore such groups.
         * @var GroupType $groupType
         */
        $groupType = $this->groupTypeRepository->findOneBy(['deLabel' => $rawGroup['group_type']]);
        $invalid_group_type = is_null($groupType);
        if($invalid_group_type) {
            $this->gelfLogger->warning(
                new SimpleLogMessage('Invalid grouptype detected, skipping group: '.$id)
            );
            return;
        }

        $statisticGroup->setId($id);
        $statisticGroup->setCanton($canton);
        $statisticGroup->setName($name);
        $statisticGroup->setParentGroup($parent);
        $statisticGroup->setGroupType($groupType);
        $batchedStatisticsRepository->add($statisticGroup);
        $this->stats[1]++;

        /** @var array $geoLocations */
        $geoLocations = $result->getContent()['linked']['geolocations'] ?? [];
        $this->createGeoLocations($statisticGroup, $geoLocations, $batchedGeoRepository);
        $this->fillHealthGroupWithStatisticGroup($statisticGroup);

        if ($groupType->getGroupType() === GroupType::CANTON) {
            $canton = $statisticGroup;
        }
        foreach ($children as $child) {
            $this->fetchGroupRecursive($child, $statisticGroup, $canton, $batchedStatisticsRepository, $batchedGeoRepository);
        }
    }

    private function createGeoLocations(StatisticGroup $group, ?array $geoLocations, BatchedRepository $batchedGeoRepository)
    {
        if (is_null($geoLocations)) {
            return;
        }
        foreach ($geoLocations as $rawGeoLocation) {
            $geoLocation = new GroupGeoLocation();
            $geoLocation->setId($rawGeoLocation['id']);
            $geoLocation->setGroup($group);
            $geoLocation->setLat($rawGeoLocation['lat']);
            $geoLocation->setLong($rawGeoLocation['long']);
            $batchedGeoRepository->add($geoLocation);
        }
    }

    /**
     * Fills in the parent and canton of the equivalent Health group with the Statistics group
     */
    private function fillHealthGroupWithStatisticGroup(StatisticGroup $statisticGroup)
    {
        /** @var Group $group */
        $group = $this->groupRepository->findOneBy(['id' => $statisticGroup->getId()]);
        if (!is_null($group)) {
            if (!is_null($statisticGroup->getCanton())) {
                $group->setCantonId($statisticGroup->getCanton()->getId());
                $group->setCantonName($statisticGroup->getCanton()->getName());
            }
            if (!is_null($statisticGroup->getParentGroup())) {
                /** @var Group $parent */
                $parent = $this->groupRepository->findOneBy(['id' => $statisticGroup->getParentGroup()->getId()]);
                if (!is_null($parent)) {
                    $group->setParentGroup($parent);
                }
            }
        }
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->stats[0], 'Fetched ' . $this->stats[1] . ' Groups in ' . number_format($this->stats[0], 2) . ' Seconds', $this->stats[1]);
    }
}
