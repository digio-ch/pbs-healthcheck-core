<?php

namespace App\Service;

use App\DTO\Mapper\InviteMapper;
use App\DTO\Model\InviteDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Entity\Security\Permission;
use App\Exception\ApiException;
use App\Repository\Midata\PersonRepository;
use App\Model\InvitationMailInput;
use App\Repository\Security\PermissionRepository;
use App\Repository\Security\PermissionTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class PermissionService
{
    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var PermissionTypeRepository $permissionTypeRepository */
    private PermissionTypeRepository $permissionTypeRepository;

    /** @var DateFormatter $dateFormatter */
    private DateFormatter $dateFormatter;

    /** @var MailService $mailService */
    private MailService $mailService;

    /** @var TranslatorInterface $translator */
    private TranslatorInterface $translator;

    /** @var PersonRepository $personRepository */
    private PersonRepository $personRepository;

    /**
     * InviteService constructor.
     * @param PermissionRepository $permissionRepository
     * @param PermissionTypeRepository $permissionTypeRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PermissionRepository $permissionRepository,
        PermissionTypeRepository $permissionTypeRepository,
        DateFormatter $dateFormatter,
        MailService $mailService,
        TranslatorInterface $translator,
        PersonRepository $personRepository
    ) {
        $this->dateFormatter = $dateFormatter;
        $this->permissionRepository = $permissionRepository;
        $this->permissionTypeRepository = $permissionTypeRepository;
        $this->mailService = $mailService;
        $this->translator = $translator;
        $this->personRepository = $personRepository;
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
     * @param PbsUserDTO $executor
     * @param InviteDTO $inviteDTO
     * @return InviteDTO
     */
    public function createInvite(Group $group, PbsUserDTO $executor, InviteDTO $inviteDTO): InviteDTO
    {
        $permission = new Permission();
        $expirationDate = new \DateTimeImmutable('+1 year');

        $permissionType = $this->permissionTypeRepository->findOneBy(['key' => $inviteDTO->getPermissionType()]);
        if (is_null($permissionType)) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'invalid permission type');
        }

        /** @var Person $owner */
        $owner = $this->personRepository->findOneBy(['id' => $executor->getId()]);

        $permission->setEmail($inviteDTO->getEmail());
        $permission->setExpirationDate($expirationDate);
        $permission->setGroup($group);
        $permission->setPermissionType($permissionType);
        $permission->setOwner($owner);
        $permission->setOwnerEmail($executor->getEmail());
        $permission->setPreExpiryNotified(false);

        $this->permissionRepository->save($permission);

        $this->sendInvitationEmail($owner, $permission);

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
     * @param Group $group
     * @param PbsUserDTO $executor
     * @param Permission $permission
     * @return InviteDTO
     */
    public function renewInvite(Group $group, PbsUserDTO $executor, Permission $permission): InviteDTO
    {
        // can not change permissions outside the group
        if ($permission->getGroup()->getId() != $group->getId()) {
            $invite = $this->translator->trans('api.entity.invite');
            $message = $this->translator->trans('api.error.notFound', ['entityName' => $invite]);
            throw new ApiException(Response::HTTP_NOT_FOUND, $message);
        }

        // only permissions that have been created manually can be extended
        if (is_null($permission->getEmail()) || is_null($permission->getExpirationDate())) {
            $invite = $this->translator->trans('api.entity.invite');
            $message = $this->translator->trans('api.error.notFound', ['entityName' => $invite]);
            throw new ApiException(Response::HTTP_NOT_FOUND, $message);
        }

        // permission is already expired
        if ($permission->getExpirationDate() <= new \DateTimeImmutable('now')) {
            $message = $this->translator->trans('api.error.invalidEntries');
            throw new ApiException(Response::HTTP_BAD_REQUEST, $message);
        }

        // permission is not expiring in the next 3 month
        if ($permission->getExpirationDate() > new \DateTimeImmutable('+3 months')) {
            $message = $this->translator->trans('api.error.invalidEntries');
            throw new ApiException(Response::HTTP_BAD_REQUEST, $message);
        }

        /** @var Person $owner */
        $owner = $this->personRepository->findOneBy(['id' => $executor->getId()]);

        $permission->setExpirationDate($permission->getExpirationDate()->add(new \DateInterval('P12M')));
        $permission->setPreExpiryNotified(false);
        $permission->setOwner($owner);
        $permission->setOwnerEmail($executor->getEmail());

        $this->permissionRepository->save($permission);

        $this->sendRenewalEmail($owner, $permission);

        return InviteMapper::createFromEntity($permission);
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

    private function formatGroupName(Group $group): string
    {
        $str = $group->getName();

        if (is_null($group->getCantonId()) || $group->getId() === $group->getCantonId()) {
            return $str;
        }

        return $str . " (" . $group->getCantonName() . ")";
    }

    /**
     * @param Person $owner
     * @param Permission $permission
     * @return void
     */
    private function sendInvitationEmail(Person $owner, Permission $permission): void
    {
        $input = (new InvitationMailInput())
            ->setSubject($this->translator->trans('email.invitation.new.subject'))
            ->setTitle($this->translator->trans('email.invitation.new.title'))
            ->setSections([
                $this->translator->trans('email.invitation.new.intro', [
                    'owner' => $owner->getNickName(),
                    'group' => $this->formatGroupName($permission->getGroup()),
                    'role' => $this->translator->trans('permissions.' . $permission->getPermissionType()->getKey())
                ]),
                $this->translator->trans('email.invitation.new.context')
            ])
            ->setLink('/login')
            ->setCtaText($this->translator->trans('email.invitation.new.cta'));

        $this->mailService->sendInvitationMail($permission->getEmail(), $input);
    }

    /**
     * @param Person $owner
     * @param Permission $permission
     * @return void
     */
    private function sendRenewalEmail(Person $owner, Permission $permission): void
    {
        $input = (new InvitationMailInput())
            ->setSubject($this->translator->trans('email.invitation.renew.subject'))
            ->setTitle($this->translator->trans('email.invitation.renew.title'))
            ->setSections([
                $this->translator->trans('email.invitation.renew.intro', [
                    'owner' => $owner->getNickName(),
                    'group' => $this->formatGroupName($permission->getGroup()),
                    'role' => $this->translator->trans('permissions.' . $permission->getPermissionType()->getKey())
                ]),
                $this->translator->trans('email.invitation.renew.nex_expiration_date', [
                    'expirationDate' =>   $this->dateFormatter->formatLong($permission->getExpirationDate())
                ]),
                $this->translator->trans('email.invitation.renew.context')
            ])
            ->setLink('/login')
            ->setCtaText($this->translator->trans('email.invitation.renew.cta'));

        $this->mailService->sendInvitationMail($permission->getEmail(), $input);
    }

    public function sendPreExpiryEmailForInvitee(Permission $permission, bool $mentionOwner): void
    {
        $input = (new InvitationMailInput())
            ->setSubject($this->translator->trans('email.invitation.pre_expiry.invitee.subject'))
            ->setTitle($this->translator->trans('email.invitation.pre_expiry.invitee.title'))
            ->setSections([
                $this->translator->trans('email.invitation.pre_expiry.invitee.intro', [
                    'group' => $this->formatGroupName($permission->getGroup()),
                    'role' => $this->translator->trans('permissions.' . $permission->getPermissionType()->getKey()),
                    'expirationDate' =>   $this->dateFormatter->formatLong($permission->getExpirationDate())
                ]),
                $this->getPreExpiryInviteeContext($permission, $mentionOwner)
            ]);

        $this->mailService->sendInvitationMail($permission->getEmail(), $input);
    }

    public function sendPreExpiryEmailForCreator(Permission $permission): void
    {
        $input = (new InvitationMailInput())
            ->setSubject($this->translator->trans('email.invitation.pre_expiry.creator.subject'))
            ->setTitle($this->translator->trans('email.invitation.pre_expiry.creator.title'))
            ->setSections([
                $this->translator->trans('email.invitation.pre_expiry.creator.intro', [
                    'invitee' => $permission->getEmail(),
                    'group' => $this->formatGroupName($permission->getGroup()),
                    'role' => $this->translator->trans('permissions.' . $permission->getPermissionType()->getKey()),
                    'expirationDate' =>   $this->dateFormatter->formatLong($permission->getExpirationDate())
                ]),
                $this->translator->trans('email.invitation.pre_expiry.creator.context')
            ])
            ->setLink('/login')
            ->setCtaText($this->translator->trans('email.invitation.pre_expiry.creator.cta'));

        $this->mailService->sendInvitationMail($permission->getOwnerEmail(), $input);
    }

    private function getPreExpiryInviteeContext(Permission $permission, bool $mentionOwner): string
    {
        $owner = $permission->getOwner();

        if (!$mentionOwner || is_null($owner)) {
            return $this->translator->trans('email.invitation.pre_expiry.invitee.context.without_owner');
        }

        $email = $permission->getOwnerEmail();
        assert(!is_null($email));

        return $this->translator->trans('email.invitation.pre_expiry.invitee.context.with_owner', [
            'ownerNickname' => $owner->getNickname(),
            'ownerEmail' => $email,
        ]);
    }
}
