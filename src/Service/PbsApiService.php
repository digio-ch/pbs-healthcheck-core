<?php

namespace App\Service;

use App\Service\Http\GuzzleWrapper;

class PbsApiService
{
    /** @var GuzzleWrapper */
    protected $guzzleWrapper;
    /** @var string */
    protected $url;
    /** @var string */
    protected $apiKey;

    /**
     * PbsApiService constructor.
     * @param GuzzleWrapper $guzzleWrapper
     * @param string $url
     * @param string $apiKey
     */
    public function __construct(GuzzleWrapper $guzzleWrapper, string $url, string $apiKey)
    {
        $this->guzzleWrapper = $guzzleWrapper;
        $this->url = $url;
        $this->apiKey = $apiKey;
    }


    /**
     * @param string $tableName
     * @param int|null $page
     * @param int|null $itemsPerPage
     * @return Http\CurlResponse
     */
    public function getTableData(string $tableName, int $page = null, int $itemsPerPage = null)
    {
        $endpoint = $this->url . '/group_health/' . $tableName;
        if ($page !== null && $itemsPerPage !== null) {
            $endpoint .= '?page=' . $page . '&size=' . $itemsPerPage;
        }
        $additionalHeaders = ['X-Token' => $this->apiKey];
        return $this->guzzleWrapper->getJson($endpoint, null, $additionalHeaders);
    }
}
