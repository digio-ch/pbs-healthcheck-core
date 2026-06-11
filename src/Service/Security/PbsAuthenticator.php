<?php

namespace App\Service\Security;

use App\Service\Pbs\PbsAuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * PbsAuthenticator implements a custom authenticator that works with PBS OAuth.
 * AbstractAuthenticator implements methods used for authentication (oauth/v2/code endpoint)
 * (see https://symfony.com/doc/current/security/custom_authenticator.html)
 * AuthenticationEntryPointInterface handles the response that is sent when an unauthenticated user
 * tries to access a protected endpoint.
 * (see https://symfony.com/doc/current/security/entry_point.html)
 */
class PbsAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const LOGIN_ROUTE = 'app_oauth';

    private PbsAuthService $pbsAuthService;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(
        PbsAuthService $pbsAuthService,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->pbsAuthService = $pbsAuthService;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * Defines the response for when an unauthenticated user tries to access a protected endpoint
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse($this->translator->trans('auth.unauthorized'), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Checks whether the current request supports authentication (only the oauth/v2/code endpoint)
     */
    public function supports(Request $request): ?bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $credentials = json_decode($request->getContent(), true);

        if (!is_array($credentials) || !array_key_exists('code', $credentials)) {
            throw new CustomUserMessageAuthenticationException('Invalid or missing credentials.');
        }

        $code = $credentials['code'];
        $locale = $this->requestStack->getCurrentRequest()->getLocale() ?? 'en';

        // fetch the user using the provided code
        $user = $this->pbsAuthService->getUser($code, $locale);

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), function () use ($user) {
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse($exception->getMessageKey(), Response::HTTP_UNAUTHORIZED);
    }
}
