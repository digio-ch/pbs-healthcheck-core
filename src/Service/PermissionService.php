<?php

namespace App\Service;

use App\DTO\Mapper\InviteMapper;
use App\DTO\Model\InviteDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\Permission;
use App\Entity\PermissionType;
use App\Exception\ApiException;
use App\Repository\PermissionRepository;
use App\Repository\PermissionTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionService
{
    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var PermissionTypeRepository $permissionTypeRepository */
    private PermissionTypeRepository $permissionTypeRepository;

    /**
     * InviteService constructor.
     * @param PermissionRepository $permissionRepository
     * @param PermissionTypeRepository $permissionTypeRepository
     */
    public function __construct(
        PermissionRepository $permissionRepository,
        PermissionTypeRepository $permissionTypeRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->permissionTypeRepository = $permissionTypeRepository;
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
    public function createInvite(Group $group, InviteDTO $inviteDTO): InviteDTO
    {
        $permission = new Permission();
        $expirationDate = (new \DateTimeImmutable())->add(new \DateInterval('P12M'));

        $permissionType = $this->permissionTypeRepository->findOneBy(['key' => $inviteDTO->getPermissionType()]);
        if (is_null($permissionType)) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'invalid permission type');
        }

        $permission->setEmail($inviteDTO->getEmail());
        $permission->setExpirationDate($expirationDate);
        $permission->setGroup($group);
        $permission->setPermissionType($permissionType);

        $this->permissionRepository->save($permission);

        return InviteMapper::createFromEntity($permission);
    }

    /**
     * @param Group $group
     * @return array
     */
    public function getAllInvites(Group $group): array
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
