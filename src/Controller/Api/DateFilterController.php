<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Service\DateFilterService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DateFilterController extends AbstractController
{
    public function __construct(private readonly DateFilterService $dateFilterService)
    {
    }

    public function getDateFilterData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->dateFilterService->getAvailableDates($group);
        return $this->json($data);
    }
}
