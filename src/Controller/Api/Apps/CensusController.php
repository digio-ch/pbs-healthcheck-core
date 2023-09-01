<?php

namespace App\Controller\Api\Apps;

use App\Entity\Midata\Group;
use App\Service\DataProvider\CensusDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

class CensusController extends AbstractController
{
    private CensusDataProvider $censusDataProvider;

    public function __construct (
        CensusDataProvider $censusDataProvider
    )
    {
        $this->censusDataProvider = $censusDataProvider;
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getPreview(Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);
        return $this->json($this->censusDataProvider->getPreviewData($group));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getTableData(Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);
        return $this->json($this->censusDataProvider->getTableData($group));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getMembersData(Group $group)
    {
        return $this->json([1,2,3,4]);
    }
}
