<?php

namespace App\Service;

use App\Service\Aggregator\AggregatorRegistry;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\PbsApi\Fetcher\CampsFetcher;
use App\Service\PbsApi\Fetcher\CoursesFetcher;
use App\Service\PbsApi\Fetcher\GroupFetcher;
use App\Service\PbsApi\Fetcher\PeopleFetcher;
use Doctrine\ORM\EntityManagerInterface;

class SyncService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PbsApiService
     */
    private $pbsApiService;
    /**
     * @var GroupFetcher
     */
    private $groupFetcher;
    /**
     * @var PeopleFetcher
     */
    private $peopleFetcher;
    /**
     * @var CampsFetcher
     */
    private $campsFetcher;
    /**
     * @var CoursesFetcher
     */
    private $coursesFetcher;

    /**
     * @var AggregatorRegistry
     */
    protected $aggregatorRegistry;

    /**
     * @param PbsApiService $pbsApiService
     */
    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, GroupFetcher $groupMapper, PeopleFetcher $peopleFetcher, CampsFetcher $campsFetcher, CoursesFetcher $coursesFetcher, AggregatorRegistry $aggregatorRegistry)
    {
        $this->em = $em;
        $this->pbsApiService = $pbsApiService;
        $this->groupFetcher = $groupMapper;
        $this->peopleFetcher = $peopleFetcher;
        $this->campsFetcher = $campsFetcher;
        $this->coursesFetcher = $coursesFetcher;
        $this->aggregatorRegistry = $aggregatorRegistry;
    }

    /**
     * @param int $groupId
     * @param $accessToken
     * @return void
     */
    public function startSync(int $groupId, $accessToken)
    {
        // First, clear all data we have on this Abteilung
        $this->clearAllData($groupId);

        // Then, fetch and persist the most up-to-date data
        $syncGroup = $this->groupFetcher->fetchAndPersistGroup($groupId, $accessToken);
        $this->peopleFetcher->fetchAndPersist($syncGroup, $accessToken);
        $this->campsFetcher->fetchAndPersist($syncGroup, $accessToken);
        $this->coursesFetcher->fetchAndPersist($syncGroup, $accessToken);

        // Finally, run the aggregations for this group
        // This runs only the necessary delta since the last aggregation, except after a fresh opt-in
        /** @var WidgetAggregator $aggregator */
        foreach ($this->aggregatorRegistry->getAggregators() as $aggregator) {
            $aggregator->aggregate($syncGroup->getId());
        }
    }

    /**
     * @param int $groupId
     * @param $accessToken
     * @return void
     */
    public function clearAllData(int $groupId)
    {
        $this->coursesFetcher->clean($groupId);
        $this->campsFetcher->clean($groupId);
        $this->peopleFetcher->clean($groupId);
        $this->groupFetcher->clean($groupId);
    }

    /**
     * @param int $groupId
     * @param $accessToken
     * @return void
     */
    public function clearAllAggregatedData(int $groupId)
    {
        // TODO implement this
    }
}
