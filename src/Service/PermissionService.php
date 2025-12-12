<?php

namespace App\Service;

use App\DTO\Mapper\InviteMapper;
use App\DTO\Model\InviteDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Entity\Security\Permission;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Model\UseCaseError;
use App\Repository\Midata\PersonRepository;
use App\Model\InvitationMailInput;
use App\Repository\Security\PermissionRepository;
use App\Repository\Security\PermissionTypeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class PermissionService
{
    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var PermissionTypeRepository $permissionTypeRepository */
    private PermissionTypeRepository $permissionTypeRepository;

    /** @var MailService $mailService */
    private MailService $mailService;

    /** @var TranslatorInterface $translator */
    private TranslatorInterface $translator;

    /** @var PersonRepository $personRepository */
    private PersonRepository $personRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * InviteService constructor.
     * @param PermissionRepository $permissionRepository
     * @param PermissionTypeRepository $permissionTypeRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PermissionRepository $permissionRepository,
        PermissionTypeRepository $permissionTypeRepository,
        MailService $mailService,
        TranslatorInterface $translator
        PermissionTypeRepository $permissionTypeRepository,
        PersonRepository $personRepository,
        TranslatorInterface $translator
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->permissionTypeRepository = $permissionTypeRepository;
        $this->personRepository = $personRepository;
        $this->translator = $translator;
        $this->mailService = $mailService;
        $this->translator = $translator;
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
     * @param PbsUserDTO $owner
     * @param InviteDTO $inviteDTO
     * @param Person $owner
     * @return InviteDTO
     */
    public function createInvite(Group $group, InviteDTO $inviteDTO, PbsUserDTO $owner): InviteDTO
    public function createInvite(Group $group, PbsUserDTO $owner, InviteDTO $inviteDTO): InviteDTO
    {
        $permission = new Permission();
        $expirationDate = (new \DateTimeImmutable())->add(new \DateInterval('P12M'));

        $permissionType = $this->permissionTypeRepository->findOneBy(['key' => $inviteDTO->getPermissionType()]);
        if (is_null($permissionType)) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'invalid permission type');
        }

        $dbOwner = $this->personRepository->findOneBy(['id' => $owner->getId()]);

        $permission->setEmail($inviteDTO->getEmail());
        $permission->setExpirationDate($expirationDate);
        $permission->setGroup($group);
        $permission->setPermissionType($permissionType);
        $permission->setOwner($dbOwner);
        $permission->setOwnerEmail($owner->getEmail());
        $permission->setPreExpiryNotified(false);

        $this->permissionRepository->save($permission);

        $input = (new InvitationMailInput())
            ->setSubject($this->translator->trans('email.invitation.new.subject'))
            ->setTitle($this->translator->trans('email.invitation.new.title'))
            ->setIntroductionText($this->translator->trans('email.invitation.new.intro', [
                'owner' => $owner->getNickName(),
                'group' => $this->formatGroupName($group),
                'role' => $this->translator->trans('permissions.' . $permissionType->getKey())
            ]))
            ->setContextText($this->translator->trans('email.invitation.new.context'))
            ->setLink('/login')
            ->setCtaText($this->translator->trans('email.invitation.new.cta'));

        $this->mailService->sendInvitationMail($permission->getEmail(), $input);

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
     * @return InviteDTO
     */
    public function renewInvite(Group $group, Permission $permission, PbsUserDTO $owner): InviteDTO
    {
        $invite = $this->translator->trans('api.entity.invite');
        $NotFoundMessage = $this->translator->trans('api.error.notFound', ['entityName' => $invite]);

        try {
            $dbPermission = $this->permissionRepository->findPermissionByGroupIDAndEmail($permission->getEmail(), $group->getId());
        } catch (NonUniqueResultException $err) {
            throw new UseCaseError($NotFoundMessage, 404, $err);
        }

        if (is_null($dbPermission))
            throw new UseCaseError($NotFoundMessage, 404);

        if ($dbPermission->isExpired())
            throw new UseCaseError($NotFoundMessage, 404);

        if (is_null($dbPermission->getOwner()))
            throw new UseCaseError($NotFoundMessage, 404);

        if (!$dbPermission->isExpiring()) {
            $message = $this->translator->trans('api.error.invalidEntries', ['entityName' => $invite]);
            throw new UseCaseError($message, 400);
        }

        $dbOwner = $this->personRepository->findOneBy(['id' => $owner->getId()]);

        $dbPermission->setExpirationDate(new \DateTimeImmutable(date('Y-m-d H:i:s.u', strtotime('+1 year'))));
        $dbPermission->setPreExpiryNotified(false);
        $dbPermission->setOwner($dbOwner);
        $dbPermission->setOwnerEmail($owner->getEmail());

        $this->permissionRepository->save($dbPermission);

        // TODO send email

        return InviteMapper::createFromEntity($dbPermission);
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

        $canton = $group->getCantonName();

        if (is_null($canton)) {
            return $str;
        }

        return $str . " (" . $canton . ")";
    }
}
