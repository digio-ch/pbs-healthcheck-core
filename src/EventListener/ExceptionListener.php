<?php

namespace App\EventListener;

use App\Exception\ApiException;
use App\Model\ApiError;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/*
 * TODO: Refactoring
 * - Remove translations of the API errors
 * - Unify the Exceptions returned (not HTTPException and ApiException)
 */

readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
        protected ParameterBagInterface $params,
        protected TranslatorInterface $translator
    ) {
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $apiError = $this->convertToApiError($event->getThrowable());
        $this->respond($event, $apiError);
    }

    private function respond(ExceptionEvent $event, ApiError $err): void
    {
        try {
            $response = new JsonResponse(
                data: $this->serializer->serialize($err, 'json'),
                status: $err->getCode(),
                json: true
            );
            $event->setResponse($response);
        } catch (ExceptionInterface $exception) {
            $this->logger->error('failed to write response', [
                'error' => $exception->getMessage()
            ]);

            $response = new Response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
            $event->setResponse($response);
        }
    }

    /**
     * Converts the given throwable to an error that the API can respond with.
     */
    private function convertToApiError(\Throwable $throwable): ApiError
    {
        if ($throwable instanceof ApiException) {
            return new ApiError(
                $throwable->getStatusCode(),
                $throwable->getMessage()
            );
        }

        if (!($throwable instanceof HttpException)) {
            return new ApiError(
                code: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $this->translator->trans('api.error.unknown'),
            );
        }

        $status = $throwable->getStatusCode();

        $prevException = $throwable->getPrevious();

        // per default validation errors are status 404, but we want 400
        if ($prevException instanceof ValidationFailedException) {
            $status = Response::HTTP_BAD_REQUEST;
        }

        return new ApiError(
            code: $status,
            message: $this->translateHTTPException($status)
        );
    }

    private function translateHTTPException(int $code): string
    {
        return match ($code) {
            Response::HTTP_BAD_REQUEST => $this->translator->trans('api.error.invalidEntries'),
            Response::HTTP_FORBIDDEN => $this->translator->trans('api.error.accessDenied'),
            default => $this->translator->trans('api.error.unknown'),
        };
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), $this->params->get('api_prefix'));
    }
}
