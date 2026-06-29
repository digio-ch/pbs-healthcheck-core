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
    public function __construct(private readonly \App\Service\DateFilterService $dateFilterService)
    {
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getDateFilterData(
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        $data = $this->dateFilterService->getAvailableDates($group);

        return $this->json($data);
    }
}
