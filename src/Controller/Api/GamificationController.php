<?php

namespace App\Controller\Api;

use App\Entity\Gamification\Goal;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Repository\Midata\GroupRepository;
use App\Service\Gamification\LoginService;
use App\Service\Gamification\PersonGamificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GamificationController extends AbstractController
{
    /**
     * Specifies whether the reset endpoint should be enabled.
     *
     * This value is injected by the environment variable GAMIFICATION_RESET_ENDPOINT_ENABLED.
     * If the variable is not present it fallbacks to false.
     */
    private bool $resetEndpointEnabled;

    public function __construct(bool $resetEndpointEnabled, private readonly LoginService $loginService, private readonly GroupRepository $groupRepository, private readonly PersonGamificationService $personGamificationService)
    {
        $this->resetEndpointEnabled = $resetEndpointEnabled;
    }

    /**
     * @param LoginService $loginService
     * @param GroupRepository $groupRepository
     */
    public function postGroupChange(
        Request $request
    ): Response {
        $json = json_decode($request->getContent(), true);
        if (is_null($json) || is_null($json['group'])) {
            throw new ApiException(400, "Invalid JSON");
        }
        $group = $this->groupRepository->find($json['group']);
        if (is_null($group)) {
            throw new ApiException(400, "Invalid Group");
        }
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $this->loginService->logByPersonAndGroup($this->getUser(), $group);
        return new Response('', Response::HTTP_CREATED);
    }

    public function usedCardLayer()
    {
        $this->personGamificationService->genericGoalProgress($this->getUser(), Goal::TYPE_CARD_LAYERS);
        return new Response('', Response::HTTP_OK);
    }

    public function usedDataFilter()
    {
        $this->personGamificationService->genericGoalProgress($this->getUser(), Goal::TYPE_DATA_FILTER);
        return new Response('', Response::HTTP_OK);
    }

    public function usedTimeFilter()
    {
        $this->personGamificationService->genericGoalProgress($this->getUser(), Goal::TYPE_TIME_FILTER);
        return new Response('', Response::HTTP_OK);
    }

    public function getUserProfile(
        Request $request
    ): JsonResponse {
        $dto = $this->personGamificationService->getPersonGamificationDTO($this->getUser(), $request->getLocale());
        return $this->json($dto);
    }

    public function checkLevel(
        Request $request
    ): JsonResponse {
        $dto = $this->personGamificationService->getCheckLevelDTO($this->getUser(), $request->getLocale());
        return $this->json($dto);
    }

    public function resetGamification(): Response
    {
        if (!$this->resetEndpointEnabled) {
            return new JsonResponse([
                "code" => 404,
                "error" => "reset endpoint is disabled"
            ], Response::HTTP_NOT_FOUND);
        }
        $this->personGamificationService->reset($this->getUser());
        return new Response('');
    }

    public function requestBetaAccess(): Response
    {
        $user = $this->getUser();
        $result = $this->personGamificationService->getBetaAccess($user);
        return $result ? new Response('', Response::HTTP_OK) : new Response('', Response::HTTP_FORBIDDEN);
    }
}
