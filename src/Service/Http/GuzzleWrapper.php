<?php

namespace App\Service\Http;

use GuzzleHttp\Utils;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class GuzzleWrapper
{
    private Client $guzzle;

    /**
     * GuzzleWrapper constructor.
     */
    public function __construct()
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        $this->guzzle = new Client(['handler' => $stack]);
    }

    public function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ): bool {
            // Limit the number of retries to 5
            if ($retries >= 5) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }
            // Retry on server errors
            return $response instanceof Response && ($response->getStatusCode() >= 500 || $response->getStatusCode() === 404);
        };
    }

    /**
     * delay 1s 2s 3s 4s 5s
     *
     * @return Closure
     */
    public function retryDelay()
    {
        return function ($numberOfRetries): int|float {
            return 1000 * $numberOfRetries;
        };
    }

    public function get(string $url, ?array $payload = null, array $header = null): CurlResponse
    {
        $response = $this->guzzle->get(
            $url,
            [
                'json' => $payload,
                'headers' => $header
            ]
        );
        return new GuzzleResponse(
            json_decode((string)$response->getBody(), true) ?? [],
            $response->getHeaders(),
            $response->getStatusCode()
        );
    }

    public function post(string $url, ?array $payload = null, array $header = []): CurlResponse
    {
        $header['Content-Type'] = 'application/json';

        $response = $this->guzzle->post(
            $url,
            [
                'body' => Utils::jsonEncode($payload),
                'headers' => $header,
            ]
        );
        return new GuzzleResponse(
            json_decode((string)$response->getBody(), true) ?? [],
            $response->getHeaders(),
            $response->getStatusCode()
        );
    }

    public function put(string $url, ?array $payload = null, array $header = null): CurlResponse
    {
        $response = $this->guzzle->put(
            $url,
            [
                'json' => $payload,
                'headers' => $header ?? []
            ]
        );
        return new GuzzleResponse(
            json_decode((string)$response->getBody(), true) ?? [],
            $response->getHeaders(),
            $response->getStatusCode()
        );
    }

    public function delete(string $url, ?array $queryParams, ?array $payload, array $header = null): CurlResponse
    {
        $response = $this->guzzle->delete(
            $url,
            [
                'query' => $queryParams,
                'json' => $payload,
                'headers' => $header ?? []
            ]
        );
        return new GuzzleResponse(
            json_decode((string)$response->getBody(), true) ?? [],
            $response->getHeaders(),
            $response->getStatusCode()
        );
    }

    public function getJson(string $url, ?string $jsonPayload = null, array $header = null): CurlResponse
    {
        return $this->get($url, json_decode($jsonPayload, true), $header);
    }

    public function postJson(string $url, ?string $jsonPayload = null, array $header = []): CurlResponse
    {
        $header['Content-Type'] = 'application/json';

        $response = $this->guzzle->post(
            $url,
            [
                'body' => $jsonPayload,
                'headers' => $header
            ]
        );
        return new GuzzleResponse(
            json_decode((string)$response->getBody(), true) ?? [],
            $response->getHeaders(),
            $response->getStatusCode()
        );
    }

    public function putJson(string $url, ?string $jsonPayload = null, array $header = null): CurlResponse
    {
        return $this->put($url, json_decode($jsonPayload ?? "", true), $header);
    }

    public function patch(string $url, ?array $payload = null, array $header = null): CurlResponse
    {
        $response = $this->guzzle->patch(
            $url,
            [
                'json' => $payload,
                'headers' => $header
            ]
        );
        return new GuzzleResponse(
            json_decode((string)$response->getBody(), true) ?? [],
            $response->getHeaders(),
            $response->getStatusCode()
        );
    }
}
