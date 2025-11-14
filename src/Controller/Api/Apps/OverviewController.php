<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\Apps\Overview\OverviewSharingDTO;
use App\Entity\Midata\Group;
use App\Exception\ApiException;
use App\Service\Apps\Overview\OverviewSharedService;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getOverviewSharing(Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);

        $isShared = $this->overviewSharedService->isShared($group->getId());

        return $this->json(new OverviewSharingDTO($isShared));
    }

    /**
     * @param Request $request
     * @param Group $group
     * @return JsonResponse
     *
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function shareOverview(Request $request, Group $group)
    {
        $this->denyAccessUnlessGranted(PermissionVoter::OWNER, $group);

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
}
