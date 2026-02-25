<?php

namespace App\Service\Logger;

use Gelf\Transport\SslOptions;

class ClientAuthSslOptions extends SslOptions
{
    /** @var string|null $clientCert */
    private $clientCert;

    /** @var string|null $clientKey */
    private $clientKey;

    /**
     * @return string|null
     */
    public function getClientCert(): ?string
    {
        return $this->clientCert;
    }

    /**
     * @param string|null $clientCert
     */
    public function setClientCert(?string $clientCert): void
    {
        $this->clientCert = $clientCert;
    }

    /**
     * @return string|null
     */
    public function getClientKey(): ?string
    {
        return $this->clientKey;
    }

    /**
     * @param string|null $clientKey
     */
    public function setClientKey(?string $clientKey): void
    {
        $this->clientKey = $clientKey;
    }

    public function toStreamContext($serverName = null)
    {
        $context = parent::toStreamContext($serverName);

        if ($this->clientCert !== null) {
            $context['ssl']['local_cert'] = $this->clientCert;
        }

        if ($this->clientKey !== null) {
            $context['ssl']['local_pk'] = $this->clientKey;
        }

        return $context;
    }
}
