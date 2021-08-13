<?php


namespace App\Controller\Api\Widget;


use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\Service\DataProvider\QuapDateDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuapController extends WidgetController
{
    public function getQuapAnswers(
        QuapDateDataProvider $dataProvider,
        DateAndDateRangeRequestData $requestData
    ): JsonResponse {
        $data = [ 1, 2, 3 ];

        return $this->json($data);
    }
}