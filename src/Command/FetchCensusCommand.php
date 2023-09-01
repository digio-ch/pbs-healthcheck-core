<?php

namespace App\Command;

use App\Entity\Midata\CensusGroup;
use App\Model\CommandStatistics;
use App\Repository\Midata\CensusGroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Service\CensusAPIService;
use App\Service\GroupStructureAPIService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchCensusCommand extends StatisticsCommand
{
    protected CensusAPIService $apiService;
    protected CensusGroupRepository $censusGroupRepository;
    protected GroupTypeRepository $groupTypeRepository;

    private SymfonyStyle $io;

    public function __construct(
        CensusAPIService $apiService,
        CensusGroupRepository $censusGroupRepository,
        GroupTypeRepository $groupTypeRepository
    )
    {
        $this->apiService = $apiService;
        $this->censusGroupRepository = $censusGroupRepository;
        $this->groupTypeRepository = $groupTypeRepository;
        parent::__construct();
    }


    public function configure()
    {
        $this->setName('app:fetch-census')
            ->setDescription('Not implemented');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $year = (int) date('Y');
        $minYear = $year - 6;
        $groupsToAggregate = [];
        // Fetch groups
        while ($year > $minYear) {
            $this->io->writeln('year ' . $year);
            $rawCensusData = $this->apiService->getCensusData($year);
            $rawCensusGroups = $rawCensusData->getContent()['census_evaluations']['groups'];
            foreach ($rawCensusGroups as $rawCensusGroup) {
                $exists = $this->censusGroupRepository->findOneBy(['group_id' => $rawCensusGroup['group_id'], 'year' => $year]);
                if(is_null($exists)) {
                    $groupsToAggregate[] = $rawCensusGroup['group_id'];
                    $this->mapRawCensusGroupToCensusGroup($rawCensusGroup, $year);
                }
            }
            $year--;
        }
        // Aggregate Groups
        foreach (array_unique($groupsToAggregate) as $groupId) {

        }
        return Command::SUCCESS;
    }

    private function mapRawCensusGroupToCensusGroup(array $rawCensusGroup, int $year)
    {
        $censusGroup = new CensusGroup();
        $censusGroup->setGroupId($this->sanitizeValue($rawCensusGroup['group_id']));
        $censusGroup->setYear($year);
        $censusGroup->setGroupType($this->groupTypeRepository->findOneBy(['groupType' => $rawCensusGroup['group_type']]));
        $censusGroup->setName($rawCensusGroup['group_name']);

        $censusGroup->setTotalCount($this->sanitizeValue($rawCensusGroup['total']['total']));
        $censusGroup->setTotalFCount($this->sanitizeValue($rawCensusGroup['total']['f']));
        $censusGroup->setTotalMCount($this->sanitizeValue($rawCensusGroup['total']['m']));

        $censusGroup->setLeiterFCount($this->sanitizeValue($rawCensusGroup['f']['leiter']));
        $censusGroup->setBiberFCount($this->sanitizeValue($rawCensusGroup['f']['biber']));
        $censusGroup->setWoelfeFCount($this->sanitizeValue($rawCensusGroup['f']['woelfe']));
        $censusGroup->setPfadisFCount($this->sanitizeValue($rawCensusGroup['f']['pfadis']));
        $censusGroup->setPiosFCount($this->sanitizeValue($rawCensusGroup['f']['pios']));
        $censusGroup->setRoverFCount($this->sanitizeValue($rawCensusGroup['f']['rover']));
        $censusGroup->setPtaFCount($this->sanitizeValue($rawCensusGroup['f']['pta']));

        $censusGroup->setLeiterMCount($this->sanitizeValue($rawCensusGroup['m']['leiter']));
        $censusGroup->setBiberMCount($this->sanitizeValue($rawCensusGroup['m']['biber']));
        $censusGroup->setWoelfeMCount($this->sanitizeValue($rawCensusGroup['m']['woelfe']));
        $censusGroup->setPfadisMCount($this->sanitizeValue($rawCensusGroup['m']['pfadis']));
        $censusGroup->setPiosMCount($this->sanitizeValue($rawCensusGroup['m']['pios']));
        $censusGroup->setRoverMCount($this->sanitizeValue($rawCensusGroup['m']['rover']));
        $censusGroup->setPtaMCount($this->sanitizeValue($rawCensusGroup['m']['pta']));
        $this->censusGroupRepository->add($censusGroup);
    }

    private function sanitizeValue($raw): int
    {
        return is_null($raw) ? 0 : $raw;
    }


    // TODO: Implement the statistics
    public function getStats(): CommandStatistics
    {
        return new CommandStatistics(0, 'Statistics not yet implemented.');
    }
}
