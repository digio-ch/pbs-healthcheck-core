<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\Apps\Overview\OverviewSharingDTO;
use App\Entity\Midata\Group;
use App\Service\Apps\Overview\OverviewSharedService;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getOverviewSharing(Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $isShared = $this->overviewSharedService->isShared($group->getId());

        return $this->json(new OverviewSharingDTO($isShared));
    }
}
