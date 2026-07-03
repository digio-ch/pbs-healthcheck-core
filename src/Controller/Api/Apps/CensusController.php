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
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function getPreview(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        return $this->json($this->censusDataProvider->getPreviewData($group));
    }

    public function getTableData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        CensusRequestData $censusRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getTableData($group, $censusRequestData);
        return $this->json($data);
    }

    public function getDevelopmentData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        CensusRequestData $censusRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getDevelopmentData($group, $censusRequestData);
        return $this->json($data);
    }


    public function getMembersData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        CensusRequestData $censusRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getMembersData($group, $censusRequestData);
        return $this->json($data);
    }

    public function getTreemapData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        CensusRequestData $censusRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = $this->censusDataProvider->getTreemapData($group, $censusRequestData);
        return $this->json($data);
    }

    /**
     *
     * @throws NonUniqueResultException
     */
    public function getFilterData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        if (!$this->isRegionOrCanton($group)) {
            throw new ApiException(403, "Only for regions and cantons");
        }
        /** @var PbsUserDTO|UserInterface|null|object $user */
        $user = $this->getUser();
        return $this->json($this->censusFilterDataProvider->getFilterData($group, $user->getId()));
    }

    /**
     *
     * @throws NonUniqueResultException
     */
    public function postFilterData(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group,
        CensusRequestData $censusRequestData
    ): JsonResponse {
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
