<?php

namespace App\Controller;

use App\DTO\Model\PbsUserDTO;
use App\Model\LogMessage\SimpleLogMessage;
use App\Service\Gamification\LoginService;
use Digio\Logging\GelfLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AuthController extends AbstractController
{
    /**
     * @var GelfLogger
     */
    private $logger;
    private LoginService $loginService;

    /**
     * AuthController constructor.
     * @param GelfLogger $logger
     */
    public function __construct(GelfLogger $logger, LoginService $loginService)
    {
        $this->logger = $logger;
        $this->loginService = $loginService;
    }

    public function login()
    {
        /** @var PbsUserDTO|UserInterface|null|object $user */
        $user = $this->getUser();
        if ($user instanceof PbsUserDTO) {
            $this->logger->info(new SimpleLogMessage(md5($user->getNickname()) . ' logged in.'));
            $this->loginService->logByUserDTOForLogin($user);
        } else {
            $this->logger->info('Non User was logged in.');
        }

        return $this->json($user, JsonResponse::HTTP_OK, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'salt', 'username']
        ]);
    }

    public function logout(Request $request, TokenStorageInterface $tokenStorage)
    {
        /** @var PbsUserDTO|UserInterface|null|object $user */
        $user = $this->getUser();

        if ($user == null) {
            return $this->json('logged out');
        }

        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        if ($user instanceof PbsUserDTO) {
            $this->logger->info(new SimpleLogMessage(md5($user->getNickName()) . ' logged out.'));
        } else {
            $this->logger->info(new SimpleLogMessage(md5($user->getUsername()) . ' logged out.'));
        }

        return $this->json('logout successful');
    }
}
