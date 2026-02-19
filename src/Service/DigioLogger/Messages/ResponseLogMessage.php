<?php


namespace App\Service\DigioLogger\Messages;

use App\Service\DigioLogger\SecretRedactor;

class ResponseLogMessage extends HttpLogMessage
{
    /** @var string */
    protected $client;

    /** @var int */
    protected $code;

    /** @var string[] */
    protected $header;

    protected $response;

    public function __construct(string $method, string $path, ?string $query, string $client, int $code, array $header, $response)
    {
        parent::__construct('', 'response', null, $method, $path, $query);
        $this->setMessage(sprintf('Request from %s to \'%s %s\' resulted in %d', $client, $method, $this->getFullPath(), $code));

        $this->client = $client;
        $this->code = $code;
        $this->header = SecretRedactor::redact($header);
        $this->response = SecretRedactor::redact($response);
    }

    /**
     * @return string
     */
    public function getClient(): string
    {
        return $this->client;
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->code;
    }

    /**
     * @return string[]
     */
    public function getResponseHeader(): array
    {
        return $this->header;
    }

    public function getResponseData()
    {
        return $this->response;
    }
}