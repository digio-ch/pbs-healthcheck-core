<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateRequestData;
use App\Service\DataProvider\MembersBirthyearDateDataProvider;
use App\Service\Security\PermissionVoter;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersBirthyearController extends WidgetController
{
    /***
     * @param DateRequestData $requestData
     * @param MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
     * @return JsonResponse
     * @throws DBALException
     */
    public function getMembersBirthyearData(
        DateRequestData $requestData,
        MembersBirthyearDateDataProvider $membersBirthyearDateDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $requestData->getGroup());

        $data = $membersBirthyearDateDataProvider->getData(
            $requestData->getGroup(),
            $requestData->getDate()->format('Y-m-d'),
            $requestData->getGroupTypes(),
            $requestData->getPeopleTypes()
        );
        return $this->json($data);
    }
}
