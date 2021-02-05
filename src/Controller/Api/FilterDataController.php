<?php

namespace App\Controller\Api;

use App\Service\DataProvider\FilterDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FilterDataController extends AbstractController
{
    /**
     * @param Request $request
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     */
    public function getFilterData(
        Request $request,
        FilterDataProvider $filterDataProvider
    ) {
        $groupId = $request->get('groupId');
        $data = $filterDataProvider->getData($groupId, $request->getLocale());

        return $this->json($data);
    }
}
