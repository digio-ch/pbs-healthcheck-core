<?php

namespace App\EventListener;

use App\Exception\ApiException;
use App\Model\ApiError;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\{
    JsonResponse, Request
};
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    private SerializerInterface $serializer;

    protected ParameterBagInterface $params;

    protected TranslatorInterface $translator;

    public function __construct(
        SerializerInterface $serializer,
        ParameterBagInterface $params,
        TranslatorInterface $translator
    ) {
        $this->serializer = $serializer;
        $this->params = $params;
        $this->translator = $translator;
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $exception = $event->getThrowable();

        $apiError = new ApiError();
        if ($exception instanceof AccessDeniedHttpException) {
            $apiError->setCode(JsonResponse::HTTP_FORBIDDEN);
            $apiError->setMessage($this->translator->trans('api.error.accessDenied'));
        } elseif (!($exception instanceof ApiException)) {
            $apiError->setCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            $apiError->setMessage($this->translator->trans('api.error.unknown'));
        } else {
            $apiError->setCode($exception->getStatusCode());
            $apiError->setMessage($exception->getMessage());
        }

        $response = new JsonResponse();
        $response->setJson($this->serializer->serialize($apiError, 'json'));
        $event->setResponse($response);
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), $this->params->get('api_prefix'));
    }
}
