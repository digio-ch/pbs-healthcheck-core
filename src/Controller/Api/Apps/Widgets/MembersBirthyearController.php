<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\MembersBirthyearDateDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersBirthyearController extends AbstractController
{
    /***
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
     * @return JsonResponse
     */
    public function getMembersBirthyearData(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData,
        MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = $membersBirthyearDateDataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRequestData->getDate()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );
        return $this->json($data);
    }

    /**
     * @param DateRequestData $dateRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @param MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
     * @return JsonResponse
     */
    public function getMembersBirthyearDataOfDepartment(
        DateRequestData $dateRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData,
        MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = $membersBirthyearDateDataProvider->getData(
            $widgetRequestData->getDepartment(),
            $dateRequestData->getDate()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );
        return $this->json($data);
    }
}
