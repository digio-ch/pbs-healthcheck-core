<?php

namespace App\Service\Security;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Group;
use App\Entity\PermissionType;
use App\Repository\PermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    public const VIEWER = 'viewer';
    public const EDITOR = 'editor';
    public const OWNER = 'owner';

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    public function __construct(
        PermissionRepository $permissionRepository
    ) {
        $this->permissionRepository = $permissionRepository;
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
