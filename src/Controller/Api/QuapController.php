<?php

namespace App\Controller\Api;

use App\DTO\Mapper\QuestionnaireMapper;
use App\DTO\Model\WidgetControllerData\WidgetRequestData;
use App\Entity\Group;
use App\Exception\ApiException;
use App\Service\QuapService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
        Request             $request,
        string              $type,
        QuestionnaireMapper $mapper
    ): JsonResponse
    {
        $date = $request->get('date', null);

        $date = $date ? DateTime::createFromFormat('Y-m-d', $date) : new DateTime("now");

        $questionnaire = $this->quapService->getQuestionnaireByType($type, $request->getLocale(), $date);

        $questionnaireDTO = QuestionnaireMapper::createQuestionnaireFromEntity($questionnaire, $request->getLocale(), $date);

        return $this->json($questionnaireDTO);
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return JsonResponse
     * @ParamConverter("post")
     */
    public function submitAnswers(
        Group   $group, // paramconverter
        Request $request
    ): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        if (is_null($json)) {
            throw new ApiException(400, "Invalid JSON");
        }

        $savedWidgetQuap = $this->quapService->submitWidgetQuapAnswers($group, $json);

        return $this->json($savedWidgetQuap->getAnswers());
    }
}