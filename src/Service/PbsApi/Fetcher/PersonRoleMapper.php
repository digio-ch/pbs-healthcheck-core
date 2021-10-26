<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Group;
use App\Entity\Person;
use App\Entity\PersonRole;
use App\Entity\Role;
use App\Repository\GroupRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class PersonRoleMapper
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PersonRoleRepository
     */
    private $personRoleRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->personRoleRepository = $this->em->getRepository(PersonRole::class);
        $this->groupRepository = $this->em->getRepository(Group::class);
        $this->roleRepository = $this->em->getRepository(Role::class);
    }

    /**
     * @param array $roleJson
     * @return PersonRole|null
     * @throws \Exception
     */
    public function mapFromJson(array $roleJson, Person $person, Group $syncGroup): ?PersonRole
    {
        $personRole = $this->personRoleRepository->findOneBy(['midataId' => $roleJson['id'], 'syncGroup' => $syncGroup]);
        if (!$personRole) {
            $personRole = new PersonRole();
            $personRole->setMidataId($roleJson['id']);
            $personRole->setSyncGroup($syncGroup);
        }
        $personRole->setPerson($person);

        $role = $this->roleRepository->getOneByRoleType($roleJson['role_class'] ?? '');
        if ($role) {
            $personRole->setRole($role);
        }

        /** @var Group $group */
        $group = $this->groupRepository->find($roleJson['links']['group'] ?? '');
        if ($group) {
            $personRole->setGroup($group);
        }

        $personRole->setCreatedAt(new DateTimeImmutable($roleJson['created_at']));
        if ($roleJson['deleted_at']) {
            $personRole->setDeletedAt(new DateTimeImmutable($roleJson['deleted_at']));
        }

        return $personRole;
    }
}
