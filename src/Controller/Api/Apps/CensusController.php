<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\Entity\Midata\Group;
use App\Service\DataProvider\CensusDataProvider;
use App\Service\DataProvider\CensusFilterDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

class CensusController extends AbstractController
{
    private CensusDataProvider $censusDataProvider;
    private CensusFilterDataProvider $censusFilterDataProvider;

    public function __construct (
        CensusDataProvider $censusDataProvider,
        CensusFilterDataProvider $censusFilterDataProvider
    )
    {
        $this->censusDataProvider = $censusDataProvider;
        $this->censusFilterDataProvider = $censusFilterDataProvider;
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getPreview(Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusDataProvider->getPreviewData($group));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getTableData(Group $group, CensusRequestData $censusRequestData)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusDataProvider->getTableData($group, $censusRequestData));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getDevelopmentData(Group $group, CensusRequestData $censusRequestData)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusDataProvider->getDevelopmentData($group, $censusRequestData));
    }


    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getMembersData(Group $group, CensusRequestData $censusRequestData)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusDataProvider->getMembersData($group, $censusRequestData));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getTreemapData(Group $group, CensusRequestData $censusRequestData)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusDataProvider->getTreemapData($group, $censusRequestData));
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getFilterData(Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusFilterDataProvider->getFilterData($group));
    }

    public function postFilterData(Group $group, CensusRequestData $censusRequestData)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        return $this->json($this->censusFilterDataProvider->setFilterData($group, $censusRequestData));
    }

}
