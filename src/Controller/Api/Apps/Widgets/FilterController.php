<?php

namespace App\Controller\Api\Apps\Widgets;

use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Service\Apps\Overview\OverviewSharedService;
use App\Service\DataProvider\FilterDataProvider;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FilterController extends AbstractController
{
    public function __construct(private readonly \App\Service\DataProvider\FilterDataProvider $filterDataProvider, private readonly \App\Service\Apps\Overview\OverviewSharedService $overviewSharedService)
    {
    }
    /**
     * @param Request $request
     * @param Group $group
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     */
    public function getFilterData(
        Request $request,
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->filterDataProvider->getData($group, $request->getLocale());
        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param Group $group
     * @param Group $department
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     */
    public function getFilterDataOfDepartment(
        Request $request,
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        #[MapEntity(mapping: ['departmentId' => 'id'])]
        Group $department
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);
        if (!$this->overviewSharedService->validateOverviewAccess($group, $department)) {
            throw new ApiException(400, "Department has to be shared and a child of the parent group");
        }
        $data = $this->filterDataProvider->getData($department, $request->getLocale());
        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param Group $group
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     */
    public function getGroupTypes(
        Request $request,
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->filterDataProvider->getGroupTypes($group, $request->getLocale());
        return $this->json($data);
    }
}
