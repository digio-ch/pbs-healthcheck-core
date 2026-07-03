<?php

namespace App\Service\Logger;

use Gelf\Transport\SslOptions;

/**
 * Wrapper around {@link SslOptions} because it does not support passing client cert and key via variable instead of file.
 */
class ClientAuthSslOptions extends SslOptions
{
    private ?string $clientCert = null;

    private ?string $clientKey = null;

    public function getClientCert(): ?string
    {
        return $this->clientCert;
    }

    public function setClientCert(?string $clientCert): void
    {
        $this->clientCert = $clientCert;
    }

    public function getClientKey(): ?string
    {
        return $this->clientKey;
    }

    public function setClientKey(?string $clientKey): void
    {
        $this->clientKey = $clientKey;
    }

    public function toStreamContext(?string $serverName = null): array
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
