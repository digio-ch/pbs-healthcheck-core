<?php

namespace App\EventListener;

use App\Exception\ApiException;
use App\Model\ApiError;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\{
    JsonResponse, Request
};
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        SerializerInterface $serializer,
        ParameterBagInterface $params,
        TranslatorInterface $translator
    ) {
        $this->serializer = $serializer;
        $this->params = $params;
        $this->translator = $translator;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $apiError = new ApiError();
            $apiError->setCode(JsonResponse::HTTP_FORBIDDEN);
            $apiError->setMessage($this->translator->trans('api.error.accessDenied'));
        } elseif (!($exception instanceof ApiException)) {
            $apiError = new ApiError();
            $apiError->setCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            $apiError->setMessage($this->translator->trans('api.error.unknown'));
        } else {
            $apiError = new ApiError();
            $apiError->setCode($exception->getStatusCode());
            $apiError->setMessage($exception->getMessage());
        }

        $response = new JsonResponse();
        $response->setJson($this->serializer->serialize($apiError, 'json'));
        $event->setResponse($response);
    }

    /**
     * @param Exception $exception
     * @return null|string
     */
    private function getTraceErrors(Exception $exception): ?string
    {
        return $this->isDebug ? (string)$exception : null;
    }

    private function isApiRequest(Request $request): bool
    {
        return strpos($request->getPathInfo(), $this->params->get('api_prefix')) === 0;
    }
}
