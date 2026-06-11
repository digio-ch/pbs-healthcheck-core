<?php

namespace App\Controller\Api;

use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Service\DateFilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DateFilterController extends AbstractController
{
    /**
     * @param Group $group
     * @param DateFilterService $dateFilterService
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getDateFilterData(
        Group $group,
        DateFilterService $dateFilterService
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        $data = $dateFilterService->getAvailableDates($group);

        return $this->json($data);
    }
}
