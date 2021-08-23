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

class GroupFetcher
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var GroupTypeRepository
     */
    private $groupTypeRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var PbsApiService
     */
    private $pbsApiService;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService) {
        $this->em = $em;
        $this->groupTypeRepository = $this->em->getRepository(GroupType::class);
        $this->groupRepository = $this->em->getRepository(Group::class);
        $this->pbsApiService = $pbsApiService;
    }

    public function fetchAndPersistGroup(string $id)
    {
        $this->em->persist($this->fetchGroup($id));
        $this->em->flush();
    }

    private function fetchGroup(string $id): Group
    {
        $groupData = $this->pbsApiService->getApiData('/groups/'.$id);
        return $this->mapJsonToGroup($groupData);
    }

    private function mapJsonToGroup(array $json): Group
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
        $group->setName($this->gr['name'] ?? null);

        $cantonId = $groupJson['links']['hierarchies'][1] ?? null;
        $group->setCantonId($cantonId);
        $group->setCantonName($this->getLinked($linked, 'groups', $cantonId));

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
            $group->addChild($this->fetchGroup($child));
        }

        return $group;
    }

    private function getLinked(array $linked, string $rel, string $id) {
        return array_filter($linked[$rel] ?? [], function($linkedEntity) use ($id) {
            return $linkedEntity['id'] === $id;
        })[0] ?? null;
    }
}
