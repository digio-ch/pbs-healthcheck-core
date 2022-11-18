<?php

namespace App\Service\Security;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Repository\Security\PermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    public const VIEWER = 'viewer';
    public const EDITOR = 'editor';
    public const OWNER = 'owner';

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var string $environment */
    private string $environment;

    /** @var array|string[] $specialAccess */
    private array $specialAccessEmails;

    public function __construct(
        PermissionRepository $permissionRepository,
        string $environment,
        string $specialAccessEmails
    ) {
        $this->permissionRepository = $permissionRepository;

        $this->environment = $environment;
        $this->specialAccessEmails = explode(',', $specialAccessEmails);
    }

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEWER, self::EDITOR, self::OWNER])) {
            return false;
        }

        if (!$subject instanceof Group) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        assert($user instanceof PbsUserDTO);
        assert($subject instanceof Group);

        // allow access if user in special email list and environment is either dev or stage
        if (
            in_array($this->environment, ['dev', 'stage']) &&
            in_array($user->getEmail(), $this->specialAccessEmails)
        ) {
            return true;
        }

        $permission = $this->permissionRepository->findHighestByIdOrEmail($subject, $user->getId(), $user->getEmail());
        if (is_null($permission)) {
            return false;
        }

        switch ($attribute) {
            case PermissionVoter::OWNER:
                return $permission->getPermissionType()->getId() === PermissionType::OWNER;
            case PermissionVoter::EDITOR:
                return $permission->getPermissionType()->getId() <= PermissionType::EDITOR;
            case PermissionVoter::VIEWER:
                return $permission->getPermissionType()->getId() <= PermissionType::VIEWER;
        }

        return false;
    }
}
