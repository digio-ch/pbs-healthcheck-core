<?php

namespace App\Service\Http;

interface CurlResponse
{
    public function getHeaderValue(string $headerKey): string;
    public function getContent();
    public function getStatusCode(): int;
}
