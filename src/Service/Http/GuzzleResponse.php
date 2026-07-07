<?php

namespace App\Service\Http;

class GuzzleResponse implements CurlResponse
{
    /**
     * @var mixed[]
     */
    private array $headers;
    /**
     * @var mixed $content
     */
    private $content;
    private int $statusCode;
    /**
     * GuzzleResponse constructor.
     * @param array $content
     */
    public function __construct($content, array $headers, int $statusCode)
    {
        $this->headers = array_change_key_case($headers, CASE_LOWER);
        $this->content = $content;
        $this->statusCode = $statusCode;
    }
    public function getHeaderValue(string $headerKey): string
    {
        return $this->headers[strtolower($headerKey)][0];
    }
    public function getContent()
    {
        return $this->content;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
