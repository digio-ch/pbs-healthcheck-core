<?php

namespace App\Service\Security;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\Group;
use App\Entity\Security\Permission;
use App\Entity\Security\PermissionType;
use App\Repository\Security\PermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    private const ORDER_OWNER = 1;
    private const ORDER_EDITOR_PLUS = 2;
    private const ORDER_EDITOR = 3;
    private const ORDER_VIEWER = 4;

    private const PERMISSION_TYPE_KEY_TO_PERMISSION_ORDER = [
        PermissionType::OWNER => PermissionVoter::ORDER_OWNER,
        PermissionType::EDITOR_PLUS => PermissionVoter::ORDER_EDITOR_PLUS,
        PermissionType::EDITOR => PermissionVoter::ORDER_EDITOR,
        PermissionType::VIEWER => PermissionVoter::ORDER_VIEWER,
    ];

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var array|string[] $specialAccess */
    private array $specialAccessEmails;

    public function __construct(
        PermissionRepository $permissionRepository,
        string $specialAccessEmails
    ) {
        $this->permissionRepository = $permissionRepository;

        $this->specialAccessEmails = explode(',', $specialAccessEmails);
    }

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [PermissionType::VIEWER, PermissionType::EDITOR, PermissionType::EDITOR_PLUS, PermissionType::OWNER])) {
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

        // allow access if user exists in SPECIAL_ACCESS variable
        if (in_array($user->getEmail(), $this->specialAccessEmails)) {
            return true;
        }

        $permission = $this->permissionRepository->findHighestByIdOrEmail($subject, $user->getId(), $user->getEmail());
        if (is_null($permission)) {
            return false;
        }

        return $this->validatePermissionAccess($permission, $attribute);
    }

    private function validatePermissionAccess(Permission $permission, string $required): bool
    {
        $PermissionTypekey = $permission->getPermissionType()->getKey();

        $actualOrder = self::PERMISSION_TYPE_KEY_TO_PERMISSION_ORDER[$PermissionTypekey];

        switch ($required) {
            case PermissionType::OWNER:
                return $actualOrder === self::ORDER_OWNER;
            case PermissionType::EDITOR_PLUS:
                return $actualOrder <= self::ORDER_EDITOR_PLUS;
            case PermissionType::EDITOR:
                return $actualOrder <= self::ORDER_EDITOR;
            case PermissionType::VIEWER:
                return $actualOrder <= self::ORDER_VIEWER;
        }

        return false;
    }
}
