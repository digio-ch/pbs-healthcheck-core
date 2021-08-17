<?php

namespace App\Controller\Api;

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
    public function getQuestionnaireData(
        Request     $request,
        QuapService $quapService,
        string      $type
    ): JsonResponse
    {
        $date = $request->get('date', null);

        $date = $date ? DateTime::createFromFormat('Y-m-d', $date) : new DateTime("now");

        return $this->json($quapService->getQuestionnaireDataByType($type, $request->getLocale(), $date));
    }

    /**
     * @param QuapService $quapService
     * @param Group $group
     * @param Request $request
     * @return JsonResponse
     * @ParamConverter("post")
     */
    public function submitAnswers(
        QuapService $quapService,
        Group       $group, // paramconverter
        Request     $request
    ): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        if (is_null($json)) {
            throw new ApiException(400, "Invalid JSON");
        }

        $savedWidgetQuap = $quapService->submitWidgetQuapAnswers($group, $json);

        return $this->json($savedWidgetQuap->getAnswers());
    }
}