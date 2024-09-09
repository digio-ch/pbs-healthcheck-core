<?php

namespace App\Controller\Api;

use App\Entity\Midata\Group;
use App\Entity\Security\Permission;
use App\Exception\ApiException;
use App\Repository\Midata\GroupRepository;
use App\Service\Gamification\LoginService;
use App\Service\Gamification\PersonGamificationService;
use App\Service\Security\PermissionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GamificationController extends AbstractController
{
    /**
     * @param Request $request
     * @param LoginService $loginService
     * @return Response
     */
    public function postGroupChange(
        Request $request,
        LoginService $loginService,
        GroupRepository $groupRepository
    ): Response {
        $json = json_decode($request->getContent(), true);
        if (is_null($json) || is_null($json['group'])) {
            throw new ApiException(400, "Invalid JSON");
        }
        $group = $groupRepository->find($json['group']);
        if (is_null($group)) {
            throw new ApiException(400, "Invalid Group");
        }
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $group);
        $loginService->logByPersonAndGroup($this->getUser(), $group);
        return new Response('', 201);
    }

    public function usedCardLayer(
        Request $request,
        PersonGamificationService $personGamificationService
    )
    {
        $personGamificationService->genericGoalProgress($this->getUser(), 'card');
        return new Response('', 200);
    }

    public function usedDataFilter(
        Request $request,
        PersonGamificationService $personGamificationService
    )
    {
        $personGamificationService->genericGoalProgress($this->getUser(), 'data');
        return new Response('', 200);
    }

    public function usedTimeFilter(
        Request $request,
        PersonGamificationService $personGamificationService
    )
    {
        $personGamificationService->genericGoalProgress($this->getUser(), 'time');
        return new Response('', 200);
    }

    public function getUserProfile(Request $request,
        PersonGamificationService $personGamificationService)
    {
        $dto = $personGamificationService->getPersonGamificationDTO($this->getUser(), $request->getLocale());
        return $this->json($dto);
    }
}
