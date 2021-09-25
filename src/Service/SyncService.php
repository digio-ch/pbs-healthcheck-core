<?php

namespace App\Service;

use App\Entity\Group;
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
     * @param PbsApiService $pbsApiService
     */
    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, GroupFetcher $groupMapper, PeopleFetcher $peopleFetcher)
    {
        $this->em = $em;
        $this->pbsApiService = $pbsApiService;
        $this->groupFetcher = $groupMapper;
        $this->peopleFetcher = $peopleFetcher;
    }

    /**
     * @param int $groupId
     * @param $accessToken
     * @return void
     */
    public function startSync(int $groupId, $accessToken)
    {
        $this->groupFetcher->fetchAndPersistGroup($groupId, $accessToken);
        $this->peopleFetcher->fetchAndPersistPeople($groupId, $accessToken);

        // TODO run aggregations here, but only for the fetched group
    }
}
