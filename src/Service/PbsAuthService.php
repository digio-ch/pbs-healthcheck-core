<?php

namespace App\Service;

use App\DTO\Mapper\GroupMapper;
use App\DTO\Mapper\PbsUserMapper;
use App\DTO\Model\GroupDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Group;
use App\Entity\Permission;
use App\Entity\PermissionType;
use App\Entity\PersonRole;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use App\Repository\PersonRoleRepository;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\Http\GuzzleWrapper;

class PbsAuthService
{
    /** @var GuzzleWrapper $guzzleWrapper */
    private GuzzleWrapper $guzzleWrapper;

    /** @var PersonRoleRepository $personRoleRepository */
    private PersonRoleRepository $personRoleRepository;

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var string $environment */
    private string $environment;

    /** @var string $pbsUrl */
    private string $pbsUrl;

    /** @var string $pbsClientId */
    private string $pbsClientId;

    /** @var string $pbsClientSecret */
    private string $pbsClientSecret;

    /** @var string $pbsCallbackUrl */
    private string $pbsCallbackUrl;

    /** @var string[] $specialAccessEmails */
    private $specialAccessEmails;

    /**
     * PbsAuthService constructor.
     * @param GuzzleWrapper $guzzleWrapper
     * @param PersonRoleRepository $personRoleRepository
     * @param GroupRepository $groupRepository
     * @param PermissionRepository $permissionRepository
     * @param string $environment
     * @param string $pbsUrl
     * @param string $pbsClientId
     * @param string $pbsClientSecret
     * @param string $pbsCallbackUrl
     * @param string $specialAccessEmails
     */
    public function __construct(
        GuzzleWrapper $guzzleWrapper,
        PersonRoleRepository $personRoleRepository,
        GroupRepository $groupRepository,
        PermissionRepository $permissionRepository,
        string $environment,
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
        $this->environment = $environment;
        $this->pbsUrl = $pbsUrl;
        $this->pbsClientId = $pbsClientId;
        $this->pbsClientSecret = $pbsClientSecret;
        $this->pbsCallbackUrl = $pbsCallbackUrl;
        $this->specialAccessEmails = explode(',', $specialAccessEmails);
    }

    /**
     * @param string $code
     * @param string $locale
     * @return PbsUserDTO
     */
    public function getUser(string $code, string $locale): PbsUserDTO
    {
        $token = $this->getTokenUsingCode($code);
        $user = $this->getUserWithToken($token);
        switch ($this->environment) {
            case 'dev':
                $this->processRolesForDev($user);
                $pbsUser = PbsUserMapper::createFromArray($user);
                $this->processGroupsForDev($pbsUser, $locale);
                break;
            case 'stage':
                $this->processRolesForStage($user);
                $pbsUser = PbsUserMapper::createFromArray($user);
                $this->processGroupsForStage($pbsUser, $locale);
                break;
            default:
                $this->processRoles($user);
                $pbsUser = PbsUserMapper::createFromArray($user);
                $this->processGroups($pbsUser, $locale);
        }

        $allGroups = $pbsUser->getGroups();
        usort($allGroups, function (GroupDTO $a, GroupDTO $b) {
            return strcmp($a->getName(), $b->getName());
        });
        $pbsUser->setGroups($allGroups);

        return $pbsUser;
    }

    /**
     * @param string $code
     * @return string
     */
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

    /**
     * @param string $token
     * @return array
     */
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
     * @param array $user
     */
    private function processRoles(array &$user)
    {
        $groupIds = array_unique(array_map(function ($role) {
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
     * This will add all groups to the user object where the user has a main-group leader role.
     * Additionally, we get all groups to where the user was invited to and them to the user object as well.
     * @param PbsUserDTO $pbsUser
     * @param string $locale
     */
    private function processGroups(PbsUserDTO $pbsUser, string $locale)
    {
        $groupMapping = [];

        $permissions = $this->permissionRepository->findAllValidByIdOrEmail($pbsUser->getId(), $pbsUser->getEmail());
        if ($permissions) {
            foreach ($permissions as $permission) {
                assert($permission instanceof Permission);

                $group = $permission->getGroup();
                $index = $group->getId();

                if (isset($groupMapping[$index])) {
                    /** @var PermissionType $currentPermissionType */
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
            /** @var Group $group */
            $group = $mapping['group'];
            /** @var PermissionType $permissionType */
            $permissionType = $mapping['permissionType'];

            $pbsUser->addGroup(GroupMapper::createFromEntity($group, $locale, $permissionType->getKey()));
        }
    }

    /**
     * @param array $user
     */
    private function processRolesForStage(array &$user)
    {
        if (!in_array($user['email'], $this->specialAccessEmails)) {
            $this->processRoles($user);
            return;
        }
        $this->processRolesForDev($user);
    }

    /**
     * @param PbsUserDTO $pbsUser
     * @param string $locale
     */
    private function processGroupsForStage(PbsUserDTO $pbsUser, string $locale)
    {
        if (!in_array($pbsUser->getEmail(), $this->specialAccessEmails)) {
            $this->processGroups($pbsUser, $locale);
            return;
        }
        $this->processGroupsForDev($pbsUser, $locale);
    }

    /**
     * This will assign a main-group leader role to every existing main group to the user.
     * We do this so we can select any main-group in the front-end.
     * @param array $user
     */
    private function processRolesForDev(array &$user)
    {
        $groups = $this->groupRepository->findAllDepartmentalParentGroups();
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
     * In dev we add all main-groups to the user object so that we can select all of them in the front-end.
     * This behaviour is only for dev, since we are working with test data from the MiData INT environment.
     * @param PbsUserDTO $pbsUser
     * @param string $locale
     */
    private function processGroupsForDev(PbsUserDTO $pbsUser, string $locale)
    {
        $groups = $this->groupRepository->findAllDepartmentalParentGroups();

        foreach ($groups as $group) {
            $pbsUser->addGroup(GroupMapper::createFromEntity($group, $locale, 'owner'));
        }
    }
}
