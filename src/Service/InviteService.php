<?php

namespace App\Service;

use App\DTO\Mapper\InviteMapper;
use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\Invite;
use App\Repository\InviteRepository;
use DateTime;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InviteService
{

    /**
     * @var InviteRepository
     */
    private $inviteRepository;

    /**
     * InviteService constructor.
     * @param InviteRepository $inviteRepository
     */
    public function __construct(InviteRepository $inviteRepository)
    {
        $this->inviteRepository = $inviteRepository;
    }

    /**
     * @param Group $group
     * @param string $email
     * @return bool
     */
    public function inviteExists(Group $group, string $email): bool
    {
        $result = $this->inviteRepository->findAllByGroupIdAndEmail($email, $group->getId());

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
        $inviteEntity = new Invite();
        $expirationDate = new DateTime();
        $expirationDate->modify('+12 month');

        $inviteEntity->setEmail($inviteDTO->getEmail());
        $inviteEntity->setExpirationDate($expirationDate);
        $inviteEntity->setGroup($group);

        $this->inviteRepository->save($inviteEntity);

        return InviteMapper::createFromEntity($inviteEntity);
    }

    /**
     * @param Group $group
     * @return array
     */
    public function getAllInvites(Group $group)
    {
        $invites = $this->inviteRepository->findByGroupId($group->getId());
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
     * @param Invite $invite
     * @param Group $group
     */
    public function deleteInvite(Invite $invite, Group $group)
    {
        if ($invite->getGroup()->getId() !== $group->getId()) {
            throw new NotFoundHttpException("Invite for current group not found");
        }
        $this->inviteRepository->remove($invite);
    }
}
