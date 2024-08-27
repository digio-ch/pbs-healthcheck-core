<?php

namespace App\Service\Gamification;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Gamification\Login;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\Gamification\LoginRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRepository;
use App\Repository\Security\PermissionRepository;

class LoginService
{
    private PersonRepository $personRepository;

    private LoginRepository $loginRepository;

    private GroupRepository $groupRepository;

    private PermissionRepository $permissionRepository;

    public function __construct(
        PersonRepository $personRepository,
        LoginRepository $loginRepository,
        GroupRepository $groupRepository,
        PermissionRepository $permissionRepository
    )
    {
        $this->personRepository = $personRepository;
        $this->loginRepository = $loginRepository;
        $this->groupRepository = $groupRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function logByUserDTOForLogin(PbsUserDTO $userDTO):Login {
        $login = new Login();

        $activeGroupDTO = $userDTO->getGroups()[0]; // Group 0 is the active group on Login.
        $activeGroup = $this->groupRepository->find($activeGroupDTO->getId());
        $user = $this->personRepository->find($userDTO->getId());
        if ($user === null) {
            throwException("user couldn't be found.");
        }
        if (sizeof($userDTO->getRoles()) !== 1) {
            throwException("Invalid amount of roles.");
        }
        $role = $this->permissionRepository->findHighestById($activeGroup, $user->getId());
        $roleKey = 'ROLE_USER';
        if (!is_null($role)) {
            $roleKey = $role->getPermissionType()->getKey();
        }

        $login->setPerson($user);
        $login->setGroup($activeGroup);
        $login->setDate(new \DateTime('now', new \DateTimeZone('Europe/Zurich')));
        $login->setIsGroupChange(false);
        $login->setRole($roleKey);

        $this->loginRepository->add($login);

        return $login;
    }

    public function logByPersonAndGroup(PbsUserDTO $userDTO, Group $group) {
        $login = new Login();
        $person = $this->personRepository->find($userDTO->getId());
        $role = $this->permissionRepository->findHighestById($group, $person->getId());
        $roleKey = 'ROLE_USER';
        if (!is_null($role)) {
            $roleKey = $role->getPermissionType()->getKey();
        }

        $login->setPerson($person);
        $login->setGroup($group);
        $login->setDate(new \DateTime('now', new \DateTimeZone('Europe/Zurich')));
        $login->setIsGroupChange(true);
        $login->setRole($roleKey);

        $this->loginRepository->add($login);

        return $login;
    }
}
