<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\Apps\Overview\OverviewSharingDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Service\Apps\Overview\OverviewSharedService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OverviewController extends AbstractController
{
    private OverviewSharedService $overviewSharedService;

    public function __construct(
        OverviewSharedService $overviewSharedService
    ) {
        $this->overviewSharedService = $overviewSharedService;
    }

    /**
     * @param Group $group
     * @return JsonResponse
     */
    public function getOverviewSharing(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        if (!$this->isDepartment($group)) {
            throw new ApiException(400, "Only for departments");
        }
        $isShared = $this->overviewSharedService->isShared($group->getId());
        return $this->json(new OverviewSharingDTO($isShared));
    }

    /**
     * @param Request $request
     * @param Group $group
     * @return JsonResponse
     */
    public function shareOverview(
        Request $request,
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::OWNER, $group);
        if (!$this->isDepartment($group)) {
            throw new ApiException(400, "Only for departments");
        }
        $json = json_decode($request->getContent(), true);
        if (is_null($json)) {
            throw new ApiException(400, "Invalid JSON");
        }
        $share = $json['share'];
        if (!is_bool($share)) {
            throw new ApiException(400, "Invalid JSON");
        }
        $this->overviewSharedService->shareOverview($group->getId(), $share);
        return $this->json(new OverviewSharingDTO($share));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function getOverviewOfDepartmentsPreview(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);
        if (!$this->isRegionOrCanton($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }
        $preview = $this->overviewSharedService->getDepartmentsPreview($group);
        return $this->json($preview);
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function getOverviewOfDepartments(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);
        if (!$this->isRegionOrCanton($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }
        $data = $this->overviewSharedService->getDepartments($group);
        return $this->json($data);
    }

    private function isDepartment(Group $group): bool
    {
        return $group->getGroupType()->getGroupType() === GroupType::DEPARTMENT;
    }

    private function isRegionOrCanton(Group $group): bool
    {
        $groupType = $group->getGroupType()->getGroupType();
        return $groupType === GroupType::CANTON || $groupType === GroupType::REGION;
    }
}
