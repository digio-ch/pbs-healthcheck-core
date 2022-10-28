<?php

namespace App\Service\Security;

use App\Repository\Midata\GroupRepository;
use App\Service\PbsAuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

class PbsAuthenticator extends AbstractGuardAuthenticator
{
    private const LOGIN_ROUTE = 'app_oauth';

    /**
     * @var PbsAuthService
     */
    private $pbsAuthService;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PbsAuthenticator constructor.
     * @param PbsAuthService $pbsAuthService
     * @param GroupRepository $groupRepository
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PbsAuthService $pbsAuthService,
        GroupRepository $groupRepository,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->pbsAuthService = $pbsAuthService;
        $this->groupRepository = $groupRepository;
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse($this->translator->trans('auth.unauthorized'), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!array_key_exists('code', $credentials)) {
            return null;
        }

        return $this->pbsAuthService->getUser($credentials['code'], $this->request->getLocale());
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // continue in login route
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
