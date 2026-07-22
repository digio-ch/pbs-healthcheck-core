<?php

namespace App\Controller\Api\Apps;

use DateTimeImmutable;
use App\DTO\Mapper\AnswersMapper;
use App\DTO\Mapper\QuestionnaireMapper;
use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\OptionalDateRequestData;
use App\Entity\Gamification\Goal;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Service\Apps\Quap\QuapService;
use App\Service\DataProvider\QuapSubdepartmentDateDataProvider;
use App\Service\Gamification\PersonGamificationService;
use App\Service\Gamification\QuapGamificationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;

class QuapController extends AbstractController
{
    private QuapService $quapService;

    public function __construct(QuapService $quapService, private readonly QuapSubdepartmentDateDataProvider $dataProvider, private readonly QuapGamificationService $quapGamificationService, private readonly PersonGamificationService $personGamificationService)
    {
        $this->quapService = $quapService;
    }

    public function getPreview(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->quapService->getAnswers(
            $group,
            null
        );
        return $this->json($data);
    }

    /**
     * @throws ApiException
     */
    public function getDepartmentPreview(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);
        try {
            $data = $this->quapService->getAnswersForSubDepartments(
                $group,
                null
            );
            return $this->json($data);
        } catch (Exception $exception) {
            throw new ApiException(400, "Invalid input");
        }
    }

    /**
     * @throws Exception
     */
    public function getAnswers(
        OptionalDateRequestData $dateRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $dateRequestData->getGroup());

        $data = $this->quapService->getAnswers(
            $dateRequestData->getGroup(),
            is_null($dateRequestData->getDate()) ? null : DateTimeImmutable::createFromMutable($dateRequestData->getDate())
        );

        return $this->json($data);
    }

    /**
     * @param QuapSubdepartmentDateDataProvider $dataProvider
     */
    public function getDepartmentsOverview(
        DateRequestData $dateRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $dateRequestData->getGroup());

        $data = $this->dataProvider->getData(
            $dateRequestData->getGroup(),
            $dateRequestData->getDate()->format('Y-m-d')
        );

        return $this->json($data);
    }

    public function getQuestionnaireData(
        Request $request,
        string $type
    ): JsonResponse {
        $date = $request->query->get('date');
        $date = $date
            ? DateTimeImmutable::createFromFormat('Y-m-d', $date)
            : new DateTimeImmutable('now');

        $questionnaire = $this->quapService->getQuestionnaireByType(
            $type,
            $request->getLocale(),
            $date->format('Y-m-d'),
        );

        $questionnaireDTO = QuestionnaireMapper::createQuestionnaireFromEntity($questionnaire, $request->getLocale());

        return $this->json($questionnaireDTO);
    }

    public function submitAnswers(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR, $group);
        $answers = json_decode($request->getContent(), true);
        if (is_null($answers)) {
            throw new ApiException(400, "Invalid JSON");
        }
        // has to be before answers are saved!
        $this->quapGamificationService->processQuapEvent($answers, $group, $this->getUser());
        $savedWidgetQuap = $this->quapService->submitAnswers($group, $answers);
        // we want to reverse sort the aspects so that the JSON parser encodes them as object instead of array
        $newAnswers = AnswersMapper::reverseSortAspects($savedWidgetQuap->getAnswers());
        return $this->json($newAnswers);
    }

    public function setAccess(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::OWNER, $group);
        $payload = json_decode($request->getContent(), true);
        if (!isset($payload['allow_access'])) {
            throw new ApiException(400, "Invalid request body");
        }
        $this->quapService->updateAllowAccess($group, $payload['allow_access']);
        $this->personGamificationService->genericGoalProgress($this->getUser(), Goal::TYPE_SHARE_EL);
        return $this->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @throws ApiException
     */
    public function getAnswersForSubDepartments(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);
        $date = $request->query->get('date');
        $date = $date
            ? DateTimeImmutable::createFromFormat('Y-m-d', $date)
            : new DateTimeImmutable('now');
        try {
            $match = in_array($group->getGroupType()->getGroupType(), GroupType::DEPARTMENTS_ALLOWING_HIERARCHY);
            if ($match === false) {
                throw new ApiException(400, "Invalid group");
            }


            $response = $this->quapService->getHierarchicalAnswersFromSubDepartments($group, $date);
            return $this->json($response);
        } catch (Exception $exception) {
            throw new ApiException(400, "Invalid input");
        }
    }
}
