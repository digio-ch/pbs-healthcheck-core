<?php

namespace App\Controller\Api;

use App\Entity\Midata\Group;
use App\Service\DataProvider\FilterDataProvider;
use App\Service\DateFilterService;
use App\Service\Security\PermissionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $data = $dateFilterService->getAvailableDates($group);

        return $this->json($data);
    }
}
