<?php

namespace App\EventListener;

use App\Model\LogMessage\IpBlockMessage;
use Digio\Logging\GelfLogger;
use Digio\Logging\Messages\ExceptionLogMessage;
use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListener
{
    /** @var string */
    private $environment;
    /** @var string */
    private $projectDir;
    /** @var GelfLogger */
    private $logger;

    /** @var string[] */
    private const ALLOWED = ['CH', 'IT', 'FR', 'DE', 'AT'];
    /** @var string[] */
    private const ACTIVE_ENVS = ['stage', 'prod'];

    /**
     * RequestListener constructor.
     * @param string $environment
     * @param string $projectDir
     * @param GelfLogger $logger
     */
    public function __construct(string $environment, string $projectDir, GelfLogger $logger)
    {
        $this->environment = $environment;
        $this->projectDir = $projectDir;
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest() || !in_array($this->environment, self::ACTIVE_ENVS)) {
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
