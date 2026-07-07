<?php

namespace App\Service\Pbs;

use App\DTO\Mapper\GroupMapper;
use App\DTO\Mapper\PbsUserMapper;
use App\DTO\Model\GroupDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\PersonRole;
use App\Entity\Security\Permission;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRoleRepository;
use App\Repository\Security\PermissionRepository;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\Http\GuzzleWrapper;

class PbsAuthService
{
    private GuzzleWrapper $guzzleWrapper;

    private PersonRoleRepository $personRoleRepository;

    private PermissionRepository $permissionRepository;

    private GroupRepository $groupRepository;

    private string $pbsUrl;

    private string $pbsClientId;

    private string $pbsClientSecret;

    private string $pbsCallbackUrl;

    /** @var string[] $specialAccessEmails */
    private array $specialAccessEmails;

    /**
     * PbsAuthService constructor.
     */
    public function __construct(
        GuzzleWrapper $guzzleWrapper,
        PersonRoleRepository $personRoleRepository,
        GroupRepository $groupRepository,
        PermissionRepository $permissionRepository,
        string $pbsUrl,
        string $pbsClientId,
        string $pbsClientSecret,
        string $pbsCallbackUrl,
        string $specialAccessEmails
    ) {
        $this->guzzleWrapper = $guzzleWrapper;
        $this->personRoleRepository = $personRoleRepository;
        $this->groupRepository = $groupRepository;
        $this->permissionRepository = $permissionRepository;
        $this->pbsUrl = $pbsUrl;
        $this->pbsClientId = $pbsClientId;
        $this->pbsClientSecret = $pbsClientSecret;
        $this->pbsCallbackUrl = $pbsCallbackUrl;
        $this->specialAccessEmails = explode(',', $specialAccessEmails);
    }

    public function getUser(string $code, string $locale): PbsUserDTO
    {
        $token = $this->getTokenUsingCode($code);
        $user = $this->getUserWithToken($token);

        $this->assignRoles($user);

        $pbsUser = PbsUserMapper::createFromArray($user);
        $this->assignGroups($pbsUser, $locale);

        $allGroups = $pbsUser->getGroups();
        usort($allGroups, function (GroupDTO $a, GroupDTO $b): int {
            return strcmp($a->getName(), $b->getName());
        });
        $pbsUser->setGroups($allGroups);

        return $pbsUser;
    }

    private function getTokenUsingCode(string $code): string
    {
        $body = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->pbsClientId,
            'client_secret' => $this->pbsClientSecret,
            'redirect_uri' => $this->pbsCallbackUrl,
            'code' => $code
        ];
        $response = $this->guzzleWrapper->postJson($this->pbsUrl . '/oauth/token', json_encode($body));
        return $response->getContent()['access_token'];
    }

    private function getUserWithToken(string $token): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Scope' => 'with_roles'
        ];
        $response = $this->guzzleWrapper->getJson($this->pbsUrl . '/oauth/profile', null, $headers);
        return $response->getContent();
    }

    /**
     * Assigns the roles from the database if the user has no special access.
     * Else the leader roles of all groups are assigned.
     * @param array<string, mixed> $user
     */
    private function assignRoles(array &$user): void
    {
        if (in_array($user['email'], $this->specialAccessEmails)) {
            $this->assignLeaderRoleOfAllGroups($user);
            return;
        }

        $this->assignOwnRoles($user);
    }

    /**
     * This will assign a main-group leader role to every existing main group to the user.
     * We do this so we can select any main-group in the front-end.
     * @param array<string, mixed> $user
     */
    private function assignLeaderRoleOfAllGroups(array &$user): void
    {
        $groups = $this->groupRepository->findAllParentGroups();
        $user['roles'] = [];
        foreach ($groups as $group) {
            $user['roles'][] = [
                'group_id' => $group->getId(),
                'group_name' => $group->getName(),
                'role_type' => WidgetAggregator::$mainGroupRoleTypes[0]
            ];
        }
    }

    /**
     * @param array<string, mixed> $user
     */
    private function assignOwnRoles(array &$user): void
    {
        $groupIds = array_unique(array_map(function (array $role) {
            return $role['group_id'];
        }, $user['roles']));
        $user['roles'] = [];

        foreach ($groupIds as $groupId) {
            $personRoles = $this->personRoleRepository->findRolesForPersonInGroup($groupId, $user['id']);

            if (!$personRoles) {
                continue;
            }

            /** @var PersonRole $personRole */
            foreach ($personRoles as $personRole) {
                $user['roles'][] = [
                    'group_id' => $personRole->getGroup()->getId(),
                    'group_name' => $personRole->getGroup()->getName(),
                    'role_type' => $personRole->getRole()->getRoleType()
                ];
            }
        }
    }

    /**
     * If the user email is in the special access array,
     * we add all main-groups to the user object so that we can select all of them in the front-end.
     */
    private function assignGroups(PbsUserDTO $pbsUser, string $locale): void
    {
        if (in_array($pbsUser->getEmail(), $this->specialAccessEmails)) {
            $this->assignOwnerAccessOfAllGroups($pbsUser, $locale);
            return;
        }

        $this->assignGroupsBasedOnPermissions($pbsUser, $locale);
    }

    /**
     * This will add all groups to the user object where the user has a main-group leader role.
     * Additionally, we get all groups to where the user was invited to and them to the user object as well.
     */
    private function assignGroupsBasedOnPermissions(PbsUserDTO $pbsUser, string $locale): void
    {
        $groupMapping = [];

        $permissions = $this->permissionRepository->findAllValidByIdOrEmail($pbsUser->getId(), $pbsUser->getEmail());
        if ($permissions) {
            foreach ($permissions as $permission) {
                assert($permission instanceof Permission);

                $group = $permission->getGroup();
                $index = $group->getId();

                if (isset($groupMapping[$index])) {
                    $currentPermissionType = $groupMapping[$index]['permissionType'];

                    if ($currentPermissionType->getId() < $permission->getId()) {
                        continue;
                    }
                }

                $groupMapping[$index] = [
                    'group' => $permission->getGroup(),
                    'permissionType' => $permission->getPermissionType(),
                ];
            }
        }

        foreach ($groupMapping as $mapping) {
            $group = $mapping['group'];
            $permissionType = $mapping['permissionType'];

            $pbsUser->addGroup(GroupMapper::createFromEntity($group, $locale, $permissionType->getKey()));
        }
    }

    /**
     * Assigns groups to the user he has permissions for.
     */
    private function assignOwnerAccessOfAllGroups(PbsUserDTO $pbsUser, string $locale): void
    {
        $groups = $this->groupRepository->findAllParentGroups();

        foreach ($groups as $group) {
            $pbsUser->addGroup(GroupMapper::createFromEntity($group, $locale, 'owner'));
        }
    }
}
