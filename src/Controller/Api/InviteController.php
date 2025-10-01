<?php

namespace App\Controller\Api;

use App\DTO\Model\InviteDTO;
use App\Entity\Gamification\Goal;
use App\Entity\Midata\Group;
use App\Entity\Security\Permission;
use App\Exception\ApiException;
use App\Service\Gamification\PersonGamificationService;
use App\Service\PermissionService;
use App\Service\Security\PermissionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InviteController extends AbstractController
{
    /**
     * @var PermissionService
     */
    private $inviteService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * InviteController constructor.
     * @param PermissionService $inviteService
     * @param TranslatorInterface $translator
     */
    public function __construct(PermissionService $inviteService, TranslatorInterface $translator)
    {
        $this->inviteService = $inviteService;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param Group $group
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     */
    public function createInvite(
        Request $request,
        Group $group,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PersonGamificationService $personGamificationService
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);

        try {
            /** @var InviteDTO $inviteDTO */
            $inviteDTO = $serializer->deserialize($request->getContent(), InviteDTO::class, 'json', [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['id', 'group', 'expirationDate']
            ]);
        } catch (\Exception $exception) {
            throw new ApiException(
                Response::HTTP_NOT_ACCEPTABLE,
                $this->translator->trans('api.error.invalidRequest')
            );
        }

        $errors = $validator->validate($inviteDTO);
        if (count($errors) > 0) {
            throw new ApiException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $this->translator->trans('api.error.invalidEntries')
            );
        }

        if ($inviteDTO->getPermissionType() === PermissionVoter::OWNER) {
            throw new ApiException(Response::HTTP_FORBIDDEN, 'You may not add group Owners.');
        }

        if ($this->inviteService->inviteExists($group, $inviteDTO->getEmail())) {
            $invite = $this->translator->trans('api.entity.invite');
            $message = $this->translator->trans('api.error.exists', ['entityName' => $invite]);
            throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
        }

        $createdInviteDTO = $this->inviteService->createInvite($group, $inviteDTO);
        $personGamificationService->genericGoalProgress($this->getUser(), Goal::TYPE_SHARE_ONE);

        return $this->json($createdInviteDTO, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Group $group
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     */
    public function getInvites(Group $group): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);

        return $this->json($this->inviteService->getAllInvites($group));
    }

    /**
     * @param Group $group
     * @param Permission $invite
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     * @ParamConverter(name="invite", options={"mapping":{"inviteId":"id"}})
     */
    public function deleteInvite(Group $group, Permission $invite): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);

        $this->inviteService->deleteInvite($invite, $group);
        $action = $this->translator->trans('api.action.deleted');
        $entity = $this->translator->trans('api.entity.invite');
        $message = $this->translator->trans('api.success', ['entityName' => $entity, 'action' => $action]);
        return $this->json($message);
    }
}
