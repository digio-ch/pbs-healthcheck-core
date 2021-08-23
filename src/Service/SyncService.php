<?php

namespace App\Service;

use App\DTO\Mapper\InviteMapper;
use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\Invite;
use App\Repository\InviteRepository;
use App\Service\PbsApi\Fetcher\GroupFetcher;
use App\Service\PbsApi\Fetcher\PeopleFetcher;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function startSync(Group $group)
    {
        $this->groupFetcher->fetchAndPersistGroup($group->getId());
        $this->peopleFetcher->fetchAndPersistPeople($group->getId());
    }
}
