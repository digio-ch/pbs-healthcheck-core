<?php

namespace App\Service\Census;

use App\Service\Http\CurlResponse;
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
     * CensusAPIService constructor.
     * @param GuzzleWrapper $guzzleWrapper
     * @param string $url
     * @param string $apiToken
     */
    public function __construct(GuzzleWrapper $guzzleWrapper, string $url, string $apiToken)
    {
        $this->guzzleWrapper = $guzzleWrapper;
        $this->url = $url;
        $this->apiToken = $apiToken;
    }


    public function getCensusData(int $year): CurlResponse
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
