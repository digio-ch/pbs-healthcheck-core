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
     * @param Group $group
     * @return void
     */
    public function startSync(Group $group, $accessToken)
    {
        $this->groupFetcher->fetchAndPersistGroup($group->getId(), $accessToken);
        $this->peopleFetcher->fetchAndPersistPeople($group->getId(), $accessToken);

        // TODO run aggregations here, but only for the fetched group
    }
}
