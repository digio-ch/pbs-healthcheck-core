<?php

namespace App\Controller\Api;

use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\Invite;
use App\Exception\ApiException;
use App\Service\InviteService;
use App\Service\PbsAuthService;
use App\Service\SyncService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     * @var PbsAuthService
     */
    private $pbsAuthService;

    /**
     * InviteController constructor.
     * @param SyncService $syncService
     * @param TranslatorInterface $translator
     */
    public function __construct(SyncService $syncService, TranslatorInterface $translator, PbsAuthService $pbsAuthService)
    {
        $this->syncService = $syncService;
        $this->translator = $translator;
        $this->pbsAuthService = $pbsAuthService;
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     */
    public function startSync(
        Request $request,
        int $groupId,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $action = $this->translator->trans('api.action.started');
        $entity = $this->translator->trans('api.entity.sync');
        $message = $this->translator->trans('api.success', ['entityName' => $entity, 'action' => $action]);
        $code = $request->toArray()['code'] ?? null;
        if ($code === null) {
            throw new BadRequestHttpException('Authorization code is missing');
        }
        $accessToken = $this->pbsAuthService->getTokenUsingCode($code, 'sync');
        $this->syncService->startSync($groupId, $accessToken);
        return $this->json($message, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     */
    public function optOut(
        Request $request,
        int $groupId,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->syncService->clearAllData($groupId);
        $this->syncService->clearAllAggregatedData($groupId);
        return $this->json(null);
    }
}
