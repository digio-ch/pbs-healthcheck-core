<?php

namespace App\Model\LogMessage;

use Digio\Logging\Messages\LogMessage;

class IpBlockMessage extends LogMessage
{
    /** @var string */
    private $isoCountryCode;
    /** @var string */
    private $clientIp;
    /** @var bool */
    private $foundByGeoIp;

    /**
     * IpBlockMessage constructor.
     * @param $isoCountryCode
     * @param $clientIp
     * @param $foundByGeoIp
     */
    public function __construct($isoCountryCode, $clientIp, $foundByGeoIp)
    {
        $this->isoCountryCode = $isoCountryCode;
        $this->clientIp = $clientIp;
        $this->foundByGeoIp = $foundByGeoIp;
        $message = 'Client IP ' . $clientIp . ' from ' . $isoCountryCode . 'was blocked.';
        parent::__construct($message, 'request');
    }

    /**
     * @return string
     */
    public function getIsoCountryCode(): string
    {
        return $this->isoCountryCode;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @return bool
     */
    public function isFoundByGeoIp(): bool
    {
        return $this->foundByGeoIp;
    }
}
