<?php

namespace App\Controller\Api;

use App\DTO\Mapper\QuestionnaireMapper;
use App\Entity\Group;
use App\Exception\ApiException;
use App\Service\QuapService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuapController extends AbstractController
{

    /**
     * @var QuapService $quapService
     */
    private $quapService;

    public function __construct(QuapService $quapService)
    {
        $this->quapService = $quapService;
    }

    public function getQuestionnaireData(
        Request $request,
        string $type
    ): JsonResponse
    {
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
     * @ParamConverter("id")
     */
    public function submitAnswers(
        Group $group,
        Request $request
    ): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        if (is_null($json)) {
            throw new ApiException(400, "Invalid JSON");
        }

        $savedWidgetQuap = $this->quapService->submitAnswers($group, $json);

        return $this->json($savedWidgetQuap->getAnswers());
    }

    public function getAnswers(
        Group $group,
        Request $request
    ): JsonResponse
    {
        $date = $request->get('date', null);
        $date = $date ? \DateTimeImmutable::createFromFormat('Y-m-d', $date) : null;

        $widgetQuap = $this->quapService->getAnswers($group, $date);

        return $this->json($widgetQuap->getAnswers());
    }
}
