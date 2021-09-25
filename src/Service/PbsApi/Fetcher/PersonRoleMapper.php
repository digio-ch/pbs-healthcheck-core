<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Group;
use App\Entity\Person;
use App\Entity\PersonRole;
use App\Entity\Role;
use App\Repository\GroupRepository;
use App\Repository\PersonRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\RoleRepository;
use App\Service\PbsApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

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
     * @var PersonRepository
     */
    private $personRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var RoleRepository
     */
    private $roleRepository;
    /**
     * @var PbsApiService
     */
    private $pbsApiService;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService) {
        $this->em = $em;
        $this->personRoleRepository = $this->em->getRepository(PersonRole::class);
        $this->personRepository = $this->em->getRepository(Person::class);
        $this->groupRepository = $this->em->getRepository(Group::class);
        $this->roleRepository = $this->em->getRepository(Role::class);
        $this->pbsApiService = $pbsApiService;
    }

    /**
     * @param array $roleJson
     * @return PersonRole|null
     * @throws \Exception
     */
    public function mapFromJson(array $roleJson, Person $person): ?PersonRole
    {
        $personRole = $this->personRoleRepository->findOneBy(['id' => $roleJson['id']]);
        if (!$personRole) {
            $personRole = new PersonRole();
            $personRole->setId($roleJson['id']);
            $metadata = $this->em->getClassMetaData(get_class($personRole));
            $metadata->setIdGenerator(new AssignedGenerator());
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
