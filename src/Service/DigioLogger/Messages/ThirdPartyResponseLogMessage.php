<?php

namespace App\Service\DigioLogger\Messages;

use App\Service\DigioLogger\SecretRedactor;

class ThirdPartyResponseLogMessage extends HttpLogMessage
{
    /** @var int $code */
    private $code;

    /** @var string[] $header */
    private $header;

    private $response;

    public function __construct(string $destination, string $method, string $path, ?string $query, int $code, array $header, $response)
    {
        parent::__construct('', 'third-party-response', $destination, $method, $path, $query);
        $this->setMessage(sprintf('Request to third-party \'%s %s\' resulted in %d', $method, $this->getFullPath(), $code));

        $this->code = $code;
        $this->header = SecretRedactor::redact($header);
        $this->response = SecretRedactor::redact($response);
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
