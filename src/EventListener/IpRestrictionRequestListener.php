<?php

namespace App\EventListener;

use App\Model\LogMessage\IpBlockMessage;
use App\Service\Logger\AppLogger;
use App\Service\Logger\Messages\ExceptionLogMessage;
use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class IpRestrictionRequestListener
{
    private string $environment;
    private string $projectDir;
    private AppLogger $logger;

    /** @var string[] */
    private const ALLOWED = ['CH', 'IT', 'FR', 'DE', 'AT'];
    /** @var string[] */
    private const ACTIVE_ENVS = ['stage', 'prod'];

    /**
     * RequestListener constructor.
     */
    public function __construct(string $environment, string $projectDir, AppLogger $logger)
    {
        $this->environment = $environment;
        $this->projectDir = $projectDir;
        $this->logger = $logger;
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !in_array($this->environment, self::ACTIVE_ENVS)) {
            return;
        }

        $isBlocked = $this->isBlocked($event->getRequest()->getClientIp());

        if ($isBlocked) {
            $response = new JsonResponse(
                ['message' => 'Client IP ' . $event->getRequest()->getClientIp() . ' blocked'],
                JsonResponse::HTTP_FORBIDDEN
            );
            $event->setResponse($response);
        }
    }

    private function isBlocked(string $ip): bool
    {
        $geoLiteDbPath = $this->projectDir . '/resources/GeoLite2/GeoLite2-Country.mmdb';
        try {
            $geoReader = new Reader($geoLiteDbPath);
            $isoCountryCode = $geoReader->country($ip)->country->isoCode;

            if (!in_array($isoCountryCode, self::ALLOWED)) {
                $this->logger->info(new IpBlockMessage($isoCountryCode, $ip, false));
                return true;
            }
        } catch (AddressNotFoundException $addressNotFoundException) {
            $this->logger->info(new IpBlockMessage('?', $ip, false));
            return true;
        } catch (Exception $e) {
            $this->logger->warning(new ExceptionLogMessage($e));
            return true;
        }
        return false;
    }
}
