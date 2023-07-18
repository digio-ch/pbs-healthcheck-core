<?php

namespace App\Controller\Api;

use App\Entity\General\GroupSettings;
use App\Entity\Midata\Group;
use App\Repository\General\GroupSettingsRepository;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class GroupSettingsController extends AbstractController
{

    /**
     * @param Request $request
     * @param Group $group
     * @param GroupSettingsRepository $groupSettingsRepository
     * @return void
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     */
    public function postRoleOverviewFilter(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager
    ) {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        $groupSettings = $group->getGroupSettings();
        $groupSettings->setRoleOverviewFilter(json_decode($request->getContent()));
        $entityManager->persist($groupSettings);
        $entityManager->flush();
        return new Response('', 204);
    }

}
