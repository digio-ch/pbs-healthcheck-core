<?php

namespace App\Service\Security;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Group;
use App\Service\Aggregator\WidgetAggregator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GroupVoter extends Voter
{
    private const VIEW = 'view';
    private const DELETE = 'delete';
    private const CREATE = 'create';

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::CREATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Group) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var PbsUserDTO $user */
        $user = $token->getUser();

        if (!$user || !$user instanceof PbsUserDTO) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::DELETE:
                return $this->hasRoleInGroup($user, $subject->getId(), WidgetAggregator::$mainGroupRoleTypes);
            case self::VIEW:
                return $this->hasRoleInGroup(
                    $user,
                    $subject->getId(),
                    array_merge(WidgetAggregator::$mainGroupRoleTypes, ['Group::Abteilung::Coach'])
                );
            default:
                return false;
        }
    }

    private function hasRoleInGroup(PbsUserDTO $user, int $groupId, array $roles): bool
    {
        foreach ($user->getPersonRoles() as $personRole) {
            if (!in_array($personRole->getRoleType(), $roles)) {
                continue;
            }
            if ($personRole->getGroupId() !== $groupId) {
                continue;
            }
            return true;
        }
        return false;
    }
}
