<?php


namespace App\Service\DigioLogger\Messages;

use App\Service\DigioLogger\SecretRedactor;

class ThirdPartyRequestLogMessage extends HttpLogMessage
{
    /** @var string[] */
    protected $header;

    protected $payload;

    public function __construct(string $destination, string $method, ?string $path, string $query, array $header, $payload)
    {
        parent::__construct('', 'third-party-request', $destination, $method, $path, $query);
        $this->setMessage(sprintf('Making third party request to \'%s %s\'', $method, $this->getFullPath()));

        $this->header = SecretRedactor::redact($header);
        $this->payload = SecretRedactor::redact($payload);
    }

    /**
     * @return string[]
     */
    public function getRequestHeader(): array
    {
        return $this->header;
    }

    /**
     * @return mixed
     */
    public function getRequestData()
    {
        return $this->payload;
    }
}