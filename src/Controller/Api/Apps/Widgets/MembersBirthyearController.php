<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\MembersBirthyearDateDataProvider;
use App\Service\Security\PermissionVoter;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersBirthyearController extends AbstractController
{
    /***
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
     * @return JsonResponse
     * @throws DBALException
     */
    public function getMembersBirthyearData(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData,
        MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());

        $data = $membersBirthyearDateDataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRequestData->getDate()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );
        return $this->json($data);
    }
}
