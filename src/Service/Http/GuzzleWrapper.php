<?php

namespace App\Service\Http;

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
    private $guzzle;

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
        ) {
            // Limit the number of retries to 5
            if ($retries >= 5) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500 || $response->getStatusCode() === 404) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * delay 1s 2s 3s 4s 5s
     *
     * @return Closure
     */
    public function retryDelay()
    {
        return function ($numberOfRetries) {
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

    public function post(string $url, ?array $payload = null, array $header = null): CurlResponse
    {
        $response = $this->guzzle->post(
            $url,
            [
                'body' => \GuzzleHttp\json_encode($payload),
                'headers' => array_merge(
                    [
                        'Content-Type' => 'application/json',
                        'Content-Length' => strlen(\GuzzleHttp\json_encode($payload ?? ""))
                    ],
                    $header ?? []
                )
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

    public function postJson(string $url, ?string $jsonPayload = null, array $header = null): CurlResponse
    {
        $response = $this->guzzle->post(
            $url,
            [
                'body' => $jsonPayload,
                'headers' => array_merge(
                    ['Content-Type' => 'application/json', 'Content-Length' => strlen($jsonPayload ?? "")],
                    $header ?? []
                )
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

//    private function logRequestMiddleware()
//    {
//        return function (callable $handler) {
//            return function (RequestInterface $request, array $options) use ($handler) {
//                $payload = $request->getBody()->getContents();
//                $json = json_decode($payload, true);
//                if ($json !== false) {
//                    $payload = $json;
//                }
//                $host = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
//                $this->logger->debug(new ThirdPartyRequestLogMessage(
//                    $host,
//                    $request->getMethod(),
//                    $request->getUri()->getPath(),
//                    $request->getUri()->getQuery(),
//                    $request->getHeaders(),
//                    $payload
//                ));
//                /** @var Promise $promise */
//                $promise = $handler($request, $options);
//                return $promise->then(function (ResponseInterface $response) use ($request, $host) {
//                    $body = $response->getBody()->getContents();
//                    $json = json_decode($body, true);
//                    if ($json !== false) {
//                        $body = $json;
//                    }
//                    $this->logger->info(new ThirdPartyResponseLogMessage(
//                        $host,
//                        $request->getMethod(),
//                        $request->getUri()->getPath(),
//                        $response->getStatusCode(),
//                        $response->getHeaders(),
//                        $body
//                    ));
//                    return $response;
//                });
//            };
//        };
//    }
}
