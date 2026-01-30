<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Service\DataProvider\CensusDataProvider;
use App\Service\DataProvider\CensusFilterDataProvider;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class CensusController extends AbstractController
{
    private CensusDataProvider $censusDataProvider;
    private CensusFilterDataProvider $censusFilterDataProvider;

    public function __construct(
        CensusDataProvider $censusDataProvider,
        CensusFilterDataProvider $censusFilterDataProvider
    ) {
        $this->censusDataProvider = $censusDataProvider;
        $this->censusFilterDataProvider = $censusFilterDataProvider;
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getPreview(Group $group): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        return $this->json($this->censusDataProvider->getPreviewData($group));
    }

    /**
     * @param Group $group
     * @param CensusRequestData $censusRequestData
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getTableData(Group $group, CensusRequestData $censusRequestData): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getTableData($group, $censusRequestData);

        return $this->json($data);
    }

    /**
     * @param Group $group
     * @param CensusRequestData $censusRequestData
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getDevelopmentData(Group $group, CensusRequestData $censusRequestData): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getDevelopmentData($group, $censusRequestData);

        return $this->json($data);
    }


    /**
     * @param Group $group
     * @param CensusRequestData $censusRequestData
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getMembersData(Group $group, CensusRequestData $censusRequestData): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getMembersData($group, $censusRequestData);

        return $this->json($data);
    }

    /**
     * @param Group $group
     * @param CensusRequestData $censusRequestData
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getTreemapData(Group $group, CensusRequestData $censusRequestData): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getTreemapData($group, $censusRequestData);

        return $this->json($data);
    }

    /**
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     * @throws NonUniqueResultException
     */
    public function getFilterData(Group $group): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isRegionOrCanton($group)) {
            throw new ApiException(403, "Only for regions and cantons");
        }

        /** @var PbsUserDTO|UserInterface|null|object $user */
        $user = $this->getUser();

        return $this->json($this->censusFilterDataProvider->getFilterData($group, $user->getId()));
    }

    /**
     * @param Group $group
     * @param CensusRequestData $censusRequestData
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     * @throws NonUniqueResultException
     */
    public function postFilterData(Group $group, CensusRequestData $censusRequestData): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isRegionOrCanton($group)) {
            throw new ApiException(403, "Only for regions and cantons");
        }

        /** @var PbsUserDTO|UserInterface|null|object $user */
        $user = $this->getUser();

        return $this->json($this->censusFilterDataProvider->setFilterData($group, $user->getId(), $censusRequestData));
    }

    private function isRegionOrCanton(Group $group): bool
    {
        $groupType = $group->getGroupType()->getGroupType();
        return $groupType === GroupType::CANTON || $groupType === GroupType::REGION;
    }
}
