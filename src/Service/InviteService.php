<?php

namespace App\Service;

use App\DTO\Mapper\InviteMapper;
use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InviteService
{

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /**
     * InviteService constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param Group $group
     * @param string $email
     * @return bool
     */
    public function inviteExists(Group $group, string $email): bool
    {
        $result = $this->permissionRepository->findAllByGroupIdAndEmail($email, $group->getId());

        if (!$result) {
            return false;
        }

        return count($result) > 0;
    }

    /**
     * @param Group $group
     * @param InviteDTO $inviteDTO
     * @return InviteDTO
     */
    public function createInvite(Group $group, InviteDTO $inviteDTO)
    {
        $inviteEntity = new Permission();
        $expirationDate = (new \DateTimeImmutable())->add(new \DateInterval('P12M'));

        $inviteEntity->setEmail($inviteDTO->getEmail());
        $inviteEntity->setExpirationDate($expirationDate);
        $inviteEntity->setGroup($group);

        $this->permissionRepository->save($inviteEntity);

        return InviteMapper::createFromEntity($inviteEntity);
    }

    /**
     * @param Group $group
     * @return array
     */
    public function getAllInvites(Group $group)
    {
        $invites = $this->permissionRepository->findByGroupId($group->getId());
        if (!$invites) {
            return [];
        }
        $dtos = [];
        foreach ($invites as $invite) {
            $dtos[] = InviteMapper::createFromEntity($invite);
        }
        return $dtos;
    }

    /**
     * @param Permission $invite
     * @param Group $group
     */
    public function deleteInvite(Permission $invite, Group $group)
    {
        if ($invite->getGroup()->getId() !== $group->getId()) {
            throw new NotFoundHttpException("Invite for current group not found");
        }
        $this->permissionRepository->remove($invite);
    }
}
