<?php

namespace App\Service;

use App\Service\Http\GuzzleWrapper;
use App\Service\Http;

class GroupStructureAPIService
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


    /**
     * Fetch a group from the Group Structure API.
     * This can return any group regardless of healthcheck opt-out or any other factor.
     * @param int $groupId
     * @return Http\CurlResponse
     */
    public function getGroup(int $groupId): Http\CurlResponse
    {
        $endpoint = $this->url . '/de/groups/' . $groupId . '.json?token=' . $this->apiToken;
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
