<?php

namespace App\Controller\Api;

use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\Invite;
use App\Exception\ApiException;
use App\Service\InviteService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SyncController extends AbstractController
{
    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * InviteController constructor.
     * @param SyncService $syncService
     * @param TranslatorInterface $translator
     */
    public function __construct(SyncService $syncService, TranslatorInterface $translator)
    {
        $this->syncService = $syncService;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param Group $group
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     * @IsGranted("create", subject="group")
     */
    public function startSync(
        Request $request,
        Group $group,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $action = $this->translator->trans('api.action.started');
        $entity = $this->translator->trans('api.entity.sync');
        $message = $this->translator->trans('api.success', ['entityName' => $entity, 'action' => $action]);
        $this->syncService->startSync($group);
        return $this->json($message, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Group $group
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     * @IsGranted("view", subject="group")
     */
    public function getInvites(Group $group)
    {
        return $this->json($this->inviteService->getAllInvites($group));
    }

    /**
     * @param Group $group
     * @param Invite $invite
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     * @ParamConverter(name="invite", options={"mapping":{"inviteId":"id"}})
     * @IsGranted("delete", subject="group")
     */
    public function deleteInvite(Group $group, Invite $invite)
    {
        $this->inviteService->deleteInvite($invite, $group);
        $action = $this->translator->trans('api.action.deleted');
        $entity = $this->translator->trans('api.entity.invite');
        $message = $this->translator->trans('api.success', ['entityName' => $entity, 'action' => $action]);
        return $this->json($message);
    }
}
