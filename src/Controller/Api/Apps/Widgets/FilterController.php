<?php

namespace App\Controller\Api\Apps\Widgets;

use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Service\Apps\Overview\OverviewSharedService;
use App\Service\DataProvider\FilterDataProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FilterController extends AbstractController
{
    /**
     * @param Request $request
     * @param Group $group
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getFilterData(
        Request $request,
        Group $group,
        FilterDataProvider $filterDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        $data = $filterDataProvider->getData($group, $request->getLocale());

        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param Group $group
     * @param Group $department
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     * @ParamConverter("department", options={"mapping": {"departmentId": "id"}})
     */
    public function getFilterDataOfDepartment(
        Request $request,
        Group $group,
        Group $department,
        FilterDataProvider $filterDataProvider,
        OverviewSharedService $overviewSharedService
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $group);

        if (!$overviewSharedService->validateOverviewAccess($group, $department)) {
            throw new ApiException(400, "Department has to be shared and a child of the parent group");
        }

        $data = $filterDataProvider->getData($department, $request->getLocale());

        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param Group $group
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getGroupTypes(
        Request $request,
        Group $group,
        FilterDataProvider $filterDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $filterDataProvider->getGroupTypes($group, $request->getLocale());
        return $this->json($data);
    }
}
