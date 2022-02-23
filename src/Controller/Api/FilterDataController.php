<?php

namespace App\Controller\Api;

use App\Entity\Group;
use App\Service\DataProvider\FilterDataProvider;
use App\Service\Security\PermissionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FilterDataController extends AbstractController
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
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $data = $filterDataProvider->getData($group, $request->getLocale());

        return $this->json($data);
    }
}
