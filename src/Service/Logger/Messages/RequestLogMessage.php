<?php

namespace App\Service\Logger\Messages;

use App\Service\Logger\SecretRedactor;

class RequestLogMessage extends HttpLogMessage
{
    /** @var string */
    protected $client;

    /** @var string[] */
    protected $header;

    protected $payload;

    public function __construct(string $method, string $path, ?string $query, string $client, array $header, $payload)
    {
        parent::__construct('', 'request', null, $method, $path, $query);
        $this->setMessage(sprintf('Client %s requested \'%s %s\'', $client, $method, $this->getFullPath()));

        $this->client = $client;
        $this->header = SecretRedactor::redact($header);
        $this->payload = SecretRedactor::redact($payload);
    }

    /**
     * @return string
     */
    public function getClient(): string
    {
        return $this->client;
    }

    /**
     * @return string[]
     */
    public function getRequestHeader(): array
    {
        return $this->header;
    }

    public function getRequestData()
    {
        return $this->payload;
    }
}
