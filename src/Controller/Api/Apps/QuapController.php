<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\Apps\CsvFile;
use App\DTO\Model\OptionalDateDTO;
use App\Service\Apps\Quap\DownloadService;
use App\Service\Apps\Quap\Exception\InvalidGroupTypeException;
use App\Service\Apps\Quap\Exception\InvalidParentGroupTypeException;
use App\Service\Apps\Quap\Exception\InvalidSubordinateGroupTypeException;
use App\Service\Apps\Quap\Exception\NoDataException;
use DateTimeImmutable;
use App\DTO\Mapper\AnswersMapper;
use App\DTO\Mapper\QuestionnaireMapper;
use App\DTO\Model\FilterRequestData\OptionalDateRequestData;
use App\Entity\Gamification\Goal;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Service\Apps\Quap\QuapService;
use App\Service\Gamification\PersonGamificationService;
use App\Service\Gamification\QuapGamificationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quap', name: 'quap_')]
class QuapController extends AbstractController
{
    public function __construct(
        private readonly QuapService $quapService,
        private readonly DownloadService $downloadService,
        private readonly QuapGamificationService $quapGamificationService,
        private readonly PersonGamificationService $personGamificationService
    ) {
    }

    #[Route('/preview', name: 'preview', methods: 'GET')]
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
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/groups/preview', name: 'preview_of_shared', methods: 'GET')]
    public function getPreviewOfShared(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);
        try {
            $data = $this->quapService->getAnswersForSubDepartments($group, null);
            return $this->json($data);
        } catch (InvalidParentGroupTypeException $_) {
            throw new ApiException(400, "Only for federations, regions and cantons");
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/questionnaire', name: 'answers', methods: 'GET')]
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
     * Endpoint is configured in routing_api.yaml because strictly it shouldn't be in this class
     *
     * TODO: Refactoring
     * - Move to it's own class
     * - Use attribute routing
     * - Add validation for the type
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function getQuestionnaireData(
        Request $request,
        string $type
    ): JsonResponse {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $request->query->getString('date'));

        if (!$date) {
            $date = new DateTimeImmutable('now');
        }

        $questionnaire = $this->quapService->getQuestionnaireByType(
            $type,
            $request->getLocale(),
            $date->format('Y-m-d'),
        );

        $questionnaireDTO = QuestionnaireMapper::createQuestionnaireFromEntity($questionnaire, $request->getLocale());

        return $this->json($questionnaireDTO);
    }

    #[Route('/questionnaire', name: 'submit_answers', methods: 'POST')]
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

    #[Route('/share', name: 'change_access', methods: 'PATCH')]
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
    #[Route('/groups', name: 'answers_of_shared', methods: 'GET')]
    public function getAnswersOfShared(
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
        } catch (InvalidParentGroupTypeException $_) {
            throw new ApiException(400, "Only for federations, regions and cantons");
        } catch (Exception $exception) {
            throw new ApiException(400, "Invalid input");
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/download', name: 'download', methods: 'GET')]
    public function download(
        #[MapEntity(mapping: ['groupId' => 'id'])] Group $group,
        #[MapQueryString] OptionalDateDTO $query,
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        try {
            $file = $this->downloadService->download($group, $query->getDate());
        } catch (Exception $e) {
            throw $this->toApiException($e);
        }

        return $this->streamCsvFile($file);
    }

    /**
     * @throws Exception
     */
    #[Route('/groups/{subordinateGroupId}/download', name: 'download_shared', methods: 'GET')]
    public function downloadShared(
        #[MapEntity(mapping: ['groupId' => 'id'])] Group $group,
        #[MapEntity(mapping: ['subordinateGroupId' => 'id'])] Group $subordinateGroup,
        #[MapQueryString] OptionalDateDTO $query,
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);

        try {
            $file = $this->downloadService->downloadShared($group, $subordinateGroup, $query->getDate());
        } catch (Exception $e) {
            throw $this->toApiException($e);
        }

        return $this->streamCsvFile($file);
    }

    /**
     * @throws Exception
     */
    #[Route('/groups/download', name: 'download_all_shared', methods: 'GET')]
    public function downloadAllShared(
        #[MapEntity(mapping: ['groupId' => 'id'])] Group $group,
        #[MapQueryString] OptionalDateDTO $query,
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);

        try {
            $file = $this->downloadService->downloadAllShared($group, $query->getDate());
        } catch (Exception $e) {
            throw $this->toApiException($e);
        }

        return $this->streamCsvFile($file);
    }

    private function streamCsvFile(CsvFile $file): StreamedResponse
    {
        return new StreamedResponse(
            callbackOrChunks: function () use ($file) {
                $stream = fopen('php://output', 'w');

                foreach ($file->getRows() as $row) {
                    fputcsv($stream, $row, ";");
                }

                fclose($stream);
            },
            headers: [
                "Content-Type" => "text/csv",
                "Content-Disposition" => HeaderUtils::makeDisposition(
                    disposition: HeaderUtils::DISPOSITION_ATTACHMENT,
                    filename: $file->getName(),
                    filenameFallback: $file->getFallbackName(),
                )
            ],
        );
    }

    private function toApiException(Exception $e): Exception
    {
        return match (true) {
            $e instanceof InvalidGroupTypeException => new ApiException(400, "Only for departments, regions and cantons", $e),
            $e instanceof InvalidParentGroupTypeException => new ApiException(400, "Only for federations, cantons and regions", $e),
            $e instanceof InvalidSubordinateGroupTypeException,
            $e instanceof NoDataException => new ApiException(404, "No data found for the given group or date", $e),
            default => $e,
        };
    }
}
