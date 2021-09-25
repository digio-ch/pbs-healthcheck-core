<?php

namespace App\Service;

use App\DTO\Mapper\GroupMapper;
use App\DTO\Mapper\PbsUserMapper;
use App\DTO\Model\GroupDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\GroupType;
use App\Repository\GroupTypeRepository;
use App\Repository\InviteRepository;
use App\Service\Http\GuzzleWrapper;

class PbsAuthService
{
    /**
     * @var GuzzleWrapper
     */
    private $guzzleWrapper;

    /**
     * @var InviteRepository
     */
    private $inviteRepository;

    /**
     * @var GroupTypeRepository
     */
    private $groupTypeRepository;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $pbsUrl;

    /**
     * @var string
     */
    private $pbsClientId;

    /**
     * @var string
     */
    private $pbsClientSecret;

    /**
     * @var string
     */
    private $pbsCallbackUrl;

    /**
     * @var string[]
     */
    private $specialAccessEmails;

    /**
     * PbsAuthService constructor.
     * @param GuzzleWrapper $guzzleWrapper
     * @param GroupTypeRepository $groupTypeRepository
     * @param InviteRepository $inviteRepository
     * @param string $environment
     * @param string $pbsUrl
     * @param string $pbsClientId
     * @param string $pbsClientSecret
     * @param string $pbsCallbackUrl
     * @param string $specialAccessEmails
     */
    public function __construct(
        GuzzleWrapper $guzzleWrapper,
        GroupTypeRepository $groupTypeRepository,
        InviteRepository $inviteRepository,
        string $environment,
        string $pbsUrl,
        string $pbsClientId,
        string $pbsClientSecret,
        string $pbsCallbackUrl,
        string $specialAccessEmails
    ) {
        $this->guzzleWrapper = $guzzleWrapper;
        $this->groupTypeRepository = $groupTypeRepository;
        $this->inviteRepository = $inviteRepository;
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
     * @param string|null $action
     * @return PbsUserDTO
     */
    public function getUser(string $code, string $locale, ?string $action = null): PbsUserDTO
    {
        $token = $this->getTokenUsingCode($code, $action);
        $user = $this->getUserWithToken($token);

        $user['syncable_groups'] = $this->getSyncableGroups($user, $locale);
        $user['readable_groups'] = $this->getReadableGroups($user, $locale);

        return PbsUserMapper::createFromArray($user);
    }

    /**
     * @param string $code
     * @param string|null $action
     * @return string
     */
    public function getTokenUsingCode(string $code, ?string $action = null): string
    {
        $callbackUrl = $this->pbsCallbackUrl . ($action ? '?action='.$action : '');
        $body = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->pbsClientId,
            'client_secret' => $this->pbsClientSecret,
            'redirect_uri' => $callbackUrl,
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
     * The user can sync any group of the correct type, in which they have a role
     * with enough permissions.
     *
     * @param array $user
     * @param string $locale
     * @return GroupDTO[]
     */
    private function getSyncableGroups(array $user, string $locale)
    {
        // Only keep roles that are allowed to sync a group
        $groups = array_filter($user['roles'], function ($role) {
            return $role['group_type'] === 'Group::Abteilung' && in_array($role['role_type'], [
                'Group::Abteilung::Abteilungsleitung',
                'Group::Abteilung::AbteilungsleitungStv',
            ]);
        });
        // Map Midata response to DTOs
        $groups = array_map(function ($group) use ($locale) {
            /** @var GroupType $groupType */
            $groupType = $this->groupTypeRepository->findOneBy(['groupType' => $group['group_type']]);
            return GroupMapper::createFromMidataOauthProfile($group, $groupType, $locale);
        }, $groups);

        return array_values($groups);
    }

    /**
     * All syncable groups are readable, as well as any groups for which the user has an invitation.
     * @param array $user
     * @param string $locale
     * @return GroupDTO[]
     */
    private function getReadableGroups(array $user, string $locale)
    {
        $invites = array_map(function ($invite) use ($locale) {
            return GroupMapper::createFromEntity($invite->getGroup(), $locale);
        }, $this->inviteRepository->findAllValidByEmail($user['email']));

        return $user['syncable_groups'] + array_values($invites);
    }
}
