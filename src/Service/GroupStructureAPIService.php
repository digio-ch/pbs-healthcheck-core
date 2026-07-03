<?php

namespace App\Service;

use App\Service\Http\CurlResponse;
use App\Service\Http\GuzzleWrapper;

class GroupStructureAPIService
{
    protected GuzzleWrapper $guzzleWrapper;
    protected string $url;
    protected string $apiToken;

    /**
     * GroupStructureAPIService constructor.
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
     */
    public function getGroup(int $groupId): CurlResponse
    {
        $endpoint = $this->url . '/de/groups/' . $groupId . '.json?token=' . $this->apiToken;
        return $this->guzzleWrapper->getJson($endpoint, null, []);
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
