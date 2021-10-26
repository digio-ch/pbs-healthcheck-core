<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Group;
use App\Entity\GroupType;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Service\PbsApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class GroupFetcher extends AbstractFetcher
{
    /**
     * @var GroupTypeRepository
     */
    private $groupTypeRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService) {
        parent::__construct($em, $pbsApiService);
        $this->groupTypeRepository = $this->em->getRepository(GroupType::class);
        $this->groupRepository = $this->em->getRepository(Group::class);
    }

    public function fetchAndPersistGroup(string $id, string $accessToken): Group
    {
        $syncGroup = $this->fetchGroup($id, $accessToken);
        $this->em->persist($syncGroup);
        $this->em->flush();
        return $syncGroup;
    }

    protected function fetchGroup(string $groupId, string $accessToken, ?Group $syncGroup = null): Group
    {
        $groupData = $this->pbsApiService->getApiData('/groups/'.$groupId, $accessToken);
        return $this->mapJsonToGroup($groupData, $accessToken, $syncGroup);
    }

    private function mapJsonToGroup(array $json, string $accessToken, ?Group $syncGroup = null): Group
    {
        $groupJson = $json['groups'][0] ?? [];
        $linked = $json['linked'] ?? [];

        $group = null;
        $group = new Group();
        $group->setMidataId($groupJson['id']);
        $group->setSyncGroup($syncGroup);
        $group->setName($groupJson['name'] ?? null);

        $cantonId = $groupJson['links']['hierarchies'][1] ?? null;
        $group->setCantonId($cantonId);
        $group->setCantonName($this->getLinked($linked, 'groups', $cantonId)['name'] ?? null);

        $group->setCreatedAt(new DateTimeImmutable($groupJson['created_at']));

        // TODO expose deleted groups in MiData API
        if ($groupJson['deleted_at']) {
            $group->setDeletedAt(new DateTimeImmutable($groupJson['deleted_at']));
        }

        /** @var GroupType $gt */
        $gt = $this->groupTypeRepository->findOneBy(['groupType' => $groupJson['group_type_class']]);
        $group->setGroupType($gt);

        if ($groupJson['links']['parent'] ?? false) {
            /** @var Group $pg */
            $pg = $this->groupRepository->find($groupJson['links']['parent']);
            if ($pg) {
                $group->setParentGroup($pg);
            }
        }

        foreach ($groupJson['links']['children'] ?? [] as $child) {
            $group->addChild($this->fetchGroup($child, $accessToken, $syncGroup ?? $group));
        }

        return $group;
    }

    protected function fetch(Group $syncGroup, string $accessToken): array {
        // Not implemented because not needed
        return [];
    }

    public function clean(string $groupId) {
        $this->em->createQueryBuilder()
            ->delete(Group::class, 'g')
            ->where('g.midataId = :group_id')
            ->setParameter('group_id', $groupId)
            ->getQuery()
            ->execute();
        $this->em->flush();
    }
}
