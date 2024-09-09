<?php

namespace App\Controller\Api\Apps;

use App\DTO\Mapper\QuestionnaireMapper;
use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\OptionalDateRequestData;
use App\Entity\Midata\Group;
use App\Exception\ApiException;
use App\Service\Apps\Quap\QuapService;
use App\Service\DataProvider\QuapSubdepartmentDateDataProvider;
use App\Service\Gamification\PersonGamificationService;
use App\Service\Gamification\QuapGamificationService;
use App\Service\Security\PermissionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuapController extends AbstractController
{
    /** @var QuapService $quapService */
    private QuapService $quapService;

    public function __construct(QuapService $quapService)
    {
        $this->quapService = $quapService;
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getPreview(
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $data = $this->quapService->getAnswers(
            $group,
            null
        );

        return $this->json($data);
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getDepartmentPreview(
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $data = $this->quapService->getAnswersForSubdepartments(
            $group,
            null
        );

        return $this->json($data);
    }

    /**
     * @param OptionalDateRequestData $dateRequestData
     * @return JsonResponse
     * @throws \Exception
     */
    public function getAnswers(
        OptionalDateRequestData $dateRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $dateRequestData->getGroup());

        $data = $this->quapService->getAnswers(
            $dateRequestData->getGroup(),
            is_null($dateRequestData->getDate()) ? null : \DateTimeImmutable::createFromMutable($dateRequestData->getDate())
        );

        return $this->json($data);
    }

    /**
     * @param QuapSubdepartmentDateDataProvider $dataProvider
     * @param DateRequestData $dateRequestData
     * @return JsonResponse
     */
    public function getDepartmentsOverview(
        QuapSubdepartmentDateDataProvider $dataProvider,
        DateRequestData $dateRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $dateRequestData->getGroup());

        $data = $dataProvider->getData(
            $dateRequestData->getGroup(),
            $dateRequestData->getDate()->format('Y-m-d')
        );

        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function getQuestionnaireData(
        Request $request,
        string $type
    ): JsonResponse {
        $date = $request->get('date', null);
        $date = $date ? \DateTimeImmutable::createFromFormat('Y-m-d', $date) : new \DateTimeImmutable('now');

        $questionnaire = $this->quapService->getQuestionnaireByType($type, $request->getLocale(), $date->format('Y-m-d'));

        $questionnaireDTO = QuestionnaireMapper::createQuestionnaireFromEntity($questionnaire, $request->getLocale());

        return $this->json($questionnaireDTO);
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function submitAnswers(
        Group $group,
        Request $request,
        QuapGamificationService $quapGamificationService
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::EDITOR, $group);

        $json = json_decode($request->getContent(), true);
        if (is_null($json)) {
            throw new ApiException(400, "Invalid JSON");
        }

        $quapGamificationService->processQuapEvent($json, $group, $this->getUser()); // has to be before answers are saved!
        $savedWidgetQuap = $this->quapService->submitAnswers($group, $json);

        return $this->json($savedWidgetQuap->getAnswers());
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return void
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function setAccess(
        Group $group,
        Request $request,
        PersonGamificationService $personGamificationService
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);

        $payload = json_decode($request->getContent(), true);
        if (!isset($payload['allow_access'])) {
            throw new ApiException(400, "Invalid request body");
        }

        $this->quapService->updateAllowAccess($group, $payload['allow_access']);
        $personGamificationService->genericGoalProgress($this->getUser(), 'shareEL');

        return $this->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getAnswersForSubdepartments(
        Group $group,
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $date = $request->get('date', null);
        $date = $date ? \DateTimeImmutable::createFromFormat('Y-m-d', $date) : null;

        $response = $this->quapService->getAnswersForSubdepartments($group, $date);

        return $this->json($response);
    }
}
