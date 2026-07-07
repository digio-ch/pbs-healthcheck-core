<?php

declare(strict_types=1);

namespace App\Service\Pbs;

use App\Service\Http\CurlResponse;
use App\Service\Http\GuzzleWrapper;

class PbsApiService
{
    protected GuzzleWrapper $guzzleWrapper;
    protected string $url;
    protected string $apiKey;

    /**
     * PbsApiService constructor.
     */
    public function __construct(GuzzleWrapper $guzzleWrapper, string $url, string $apiKey)
    {
        $this->guzzleWrapper = $guzzleWrapper;
        $this->url = $url;
        $this->apiKey = $apiKey;
    }


    /**
     * @param int|null $page
     * @param int|null $itemsPerPage
     */
    public function getTableData(string $tableName, int $page = null, int $itemsPerPage = null): CurlResponse
    {
        $endpoint = $this->url . '/group_health/' . $tableName;
        if ($page !== null && $itemsPerPage !== null) {
            $endpoint .= '?page=' . $page . '&size=' . $itemsPerPage;
        }
        $additionalHeaders = ['X-Token' => $this->apiKey];
        return $this->guzzleWrapper->getJson($endpoint, null, $additionalHeaders);
    }
}
