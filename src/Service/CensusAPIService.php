<?php

namespace App\Service;

use App\Service\Http\GuzzleWrapper;

class CensusAPIService
{
    /** @var GuzzleWrapper */
    protected $guzzleWrapper;
    /** @var string */
    protected $url;
    /** @var string */
    protected $apiToken;

    /**
     * PbsApiService constructor.
     * @param GuzzleWrapper $guzzleWrapper
     * @param string $url
     * @param string $apiKey
     */
    public function __construct(GuzzleWrapper $guzzleWrapper, string $url, string $apiToken)
    {
        $this->guzzleWrapper = $guzzleWrapper;
        $this->url = $url;
        $this->apiToken = $apiToken;
    }


    public function getCensusData(int $year): Http\CurlResponse
    {
        $endpoint = $this->url . '/group_health/census_evaluations.json?token=' . $this->apiToken . '&year=' . $year;
        return $this->guzzleWrapper->getJson($endpoint, null, []);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
