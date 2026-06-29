<?php

namespace App\Controller\Api;

use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Repository\General\GroupSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class GroupSettingsController extends AbstractController
{
    public function __construct(private readonly \Doctrine\ORM\EntityManagerInterface $entityManager)
    {
    }
    /**
     * @param Request $request
     * @param Group $group
     * @param GroupSettingsRepository $groupSettingsRepository
     * @return Response
     * @ParamConverter(name="group", options={"mapping":{"groupId":"id"}})
     */
    public function postRoleOverviewFilter(
        Request $request,
        Group $group
    ) {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $groupSettings = $group->getGroupSettings();
        $groupSettings->setRoleOverviewFilter(json_decode($request->getContent()));
        $this->entityManager->persist($groupSettings);
        $this->entityManager->flush();
        return new Response('', \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }
}
