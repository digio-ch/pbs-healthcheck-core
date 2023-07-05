<?php

namespace App\Command;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Statistics\GroupGeoLocation;
use App\Entity\Statistics\StatisticGroup;
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

        $this->geoLocationRepository->deleteAll();
        $this->statisticGroupRepository->deleteAll();
        $this->fetchGroupRecursive(2);
        //$this->fetchAllRemaining();
        //$this->logMissingBranches();

        return 1;
    }

    private function logMissingBranches()
    {
        $groups = $this->statisticGroupRepository->findBy(['parent' => null]);
        foreach ($groups as $group) {
            //$this->gelfLogger->warning(new SimpleLogMessage($this->recursiveGetChildrenHumanReadable($group)));
            $this->io->warning('This branch is missing the parent: ' . $this->recursiveGetChildrenHumanReadable($group));
        }
    }

    private function recursiveGetChildrenHumanReadable(StatisticGroup $group)
    {
        $result = ' ' . $group->getId() . ': [';
        $children = $group->getChildren();
        foreach ($children as $child) {
            $result .= $this->recursiveGetChildrenHumanReadable($child);
        }
        return $result . ']';
    }

    private function fetchAllRemaining()
    {
        for ($i = 2; $i < 11600; $i++) {
            $group = $this->statisticGroupRepository->findOneBy(['id' => $i]);
            if (is_null($group)) {
                try {
                    $result = $this->apiService->getGroup($i);
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
                } catch (ClientException $e) {
                    $this->io->error('Fetch for ' . $i . ' resultet in an error (' . $e->getCode() . ')');
                };
            }
        }
    }

    private function fetchGroupRecursive(int $id, ?StatisticGroup $canton = null)
    {
        $result = $this->apiService->getGroup($id);
        if ($result->getStatusCode() !== 200) {
            $this->io->error([
                'API call for group with id ' . $id . ' failed!',
                'HTTP status code: ' . $result->getStatusCode()
            ]);
            throw new Exception(
                'Got http status code ' . $result->getStatusCode() . ' from API. Stopped fetching data.'
            );
        }
        $statisticGroup = new StatisticGroup();

        $rawGroup = $result->getContent()['groups'][0];
        $name = trim($rawGroup['name']);
        $parentGroup = $rawGroup['links']['parent'] ?? null;
        if (!is_null($parentGroup)) {
            $parentGroup = $this->statisticGroupRepository->findOneBy(['id' => $parentGroup]);
        }
        $children = $rawGroup['links']['children'] ?? [];
        /** @var GroupType $groupType */
        $groupType = $this->groupTypeRepository->findOneBy(['deLabel' => $rawGroup['group_type']]);

        $statisticGroup->setId($id);
        $statisticGroup->setCanton($canton);
        $statisticGroup->setName($name);
        $statisticGroup->setParentGroup($parentGroup);
        $statisticGroup->setGroupType($groupType);
        $this->statisticGroupRepository->add($statisticGroup);

        /** @var array $geoLocations */
        $geoLocations = $result->getContent()['linked']['geolocations'];
        $this->createGeoLocations($statisticGroup, $geoLocations);
        $this->fillHealthGroupWithStatisticGroup($statisticGroup);

        if ($groupType->getGroupType() === GroupType::CANTON) {
            $canton = $statisticGroup;
        }
        foreach ($children as $child) {
            $this->fetchGroupRecursive($child, $canton);
        }
    }

    private function createGeoLocations(StatisticGroup $group, ?array $geoLocations)
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
            $this->geoLocationRepository->add($geoLocation);
        }
    }

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
        return new CommandStatistics(0, 'No statistics availible');
    }
}
