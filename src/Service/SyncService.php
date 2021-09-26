<?php

namespace App\Service;

use App\Entity\Group;
use App\Service\PbsApi\Fetcher\CampsFetcher;
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
     * @param PbsApiService $pbsApiService
     */
    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, GroupFetcher $groupMapper, PeopleFetcher $peopleFetcher, CampsFetcher $campsFetcher)
    {
        $this->em = $em;
        $this->pbsApiService = $pbsApiService;
        $this->groupFetcher = $groupMapper;
        $this->peopleFetcher = $peopleFetcher;
        $this->campsFetcher = $campsFetcher;
    }

    /**
     * @param int $groupId
     * @param $accessToken
     * @return void
     */
    public function startSync(int $groupId, $accessToken)
    {
        $this->groupFetcher->fetchAndPersistGroup($groupId, $accessToken);
        $this->peopleFetcher->fetchAndPersist($groupId, $accessToken);
        $this->campsFetcher->fetchAndPersist($groupId, $accessToken);

        // TODO run aggregations here, but only for the fetched group
    }
}
