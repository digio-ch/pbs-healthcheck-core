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
    public const EDITOR_PLUS = 'editor-plus';
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
        if (!in_array($attribute, [self::VIEWER, self::EDITOR, self::EDITOR_PLUS, self::OWNER])) {
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

        // allow access if user in special email list or environment is dev
        if ($this->environment === 'dev' || in_array($user->getEmail(), $this->specialAccessEmails)) {
            return true;
        }

        $permission = $this->permissionRepository->findHighestByIdOrEmail($subject, $user->getId(), $user->getEmail());
        if (is_null($permission)) {
            return false;
        }

        $id = $this->mapPermissionVoterToPermissionType($permission->getPermissionType()->getKey());

        switch ($attribute) {
            case PermissionVoter::OWNER:
                return $id === PermissionType::OWNER;
            case PermissionVoter::EDITOR_PLUS:
                return $id <= PermissionType::EDITOR_PLUS;
            case PermissionVoter::EDITOR:
                return $id <= PermissionType::EDITOR;
            case PermissionVoter::VIEWER:
                return $id <= PermissionType::VIEWER;
        }

        return false;
    }

    private function mapPermissionVoterToPermissionType(string $attribute): int
    {
        $map = [
            self::OWNER        => PermissionType::OWNER,
            self::EDITOR_PLUS  => PermissionType::EDITOR_PLUS,
            self::EDITOR       => PermissionType::EDITOR,
            self::VIEWER       => PermissionType::VIEWER,
        ];

        return $map[$attribute];
    }
}
