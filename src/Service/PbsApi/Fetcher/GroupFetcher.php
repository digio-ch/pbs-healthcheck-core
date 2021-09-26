<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Group;
use App\Entity\GroupType;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Service\PbsApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

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

    public function fetchAndPersistGroup(string $id, string $accessToken)
    {
        $this->em->persist($this->fetchGroup($id, $accessToken));
        $this->em->flush();
    }

    protected function fetchGroup(string $id, string $accessToken): Group
    {
        $groupData = $this->pbsApiService->getApiData('/groups/'.$id, $accessToken);
        return $this->mapJsonToGroup($groupData, $accessToken);
    }

    private function mapJsonToGroup(array $json, string $accessToken): Group
    {
        $groupJson = $json['groups'][0] ?? [];
        $linked = $json['linked'] ?? [];

        $group = $this->groupRepository->findOneBy(['id' => $groupJson['id']]);
        if (!$group) {
            $group = new Group();
            $group->setId($groupJson['id']);
            $metadata = $this->em->getClassMetaData(Group::class);
            $metadata->setIdGenerator(new AssignedGenerator());
        }
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
        $gt = $this->groupTypeRepository->findOneBy(['groupType' => $groupJson['type']]);
        $group->setGroupType($gt);

        if ($groupJson['links']['parent'] ?? false) {
            /** @var Group $pg */
            $pg = $this->groupRepository->find($groupJson['links']['parent']);
            if ($pg) {
                $group->setParentGroup($pg);
            }
        }

        foreach ($groupJson['links']['children'] ?? [] as $child) {
            $group->addChild($this->fetchGroup($child, $accessToken));
        }

        return $group;
    }

    protected function fetch(string $groupId, string $accessToken): array {
        // Not implemented because not needed
        return [];
    }
}
