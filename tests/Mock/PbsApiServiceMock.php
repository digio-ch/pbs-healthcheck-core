<?php


namespace App\Tests\Mock;


use App\Service\Http\GuzzleResponse;
use App\Service\Http\GuzzleWrapper;
use App\Service\Pbs\PbsApiService;

class PbsApiServiceMock extends PbsApiService
{
    const NO_FAIL = -1;
    const FAIL_FIRST = 0;
    const FAIL_LAST = 1;
    const FAIL_RANDOM = 2;
    const TABLES = [
        'people',
        'groups',
        'roles',
        'courses',
        'camps',
        'participations',
        'qualifications',
        'group_types',
        'role_types',
        'participation_types',
        'j_s_kinds',
        'camp_states',
        'qualification_kinds',
        'event_kinds'
    ];
    const SAMPLE_OBJECT = [
        'group_type' => 'Group::BundesGremium',
        'label_de' => 'Gremium',
        'label_fr' => 'Commission',
        'label_it' => 'Gremio MSS'
    ];
    /** @var array */
    public $responseData = [];
    /** @var int  */
    private $failOnRequest = self::NO_FAIL;
    /** @var int  */
    private $requestCount = 0;

    /**
     * PbsApiServiceMock constructor.
     */
    public function __construct()
    {
        parent::__construct(new GuzzleWrapper(), '', '');
    }

    public function generateTestData(int $sampleSize = 100) {
        foreach (self::TABLES as $tableName) {
            $this->responseData[$tableName] = [];
            for ($i = 0; $i < $sampleSize; $i++) {
                $this->responseData[$tableName][] = self::SAMPLE_OBJECT;
            }
        }
    }

    public function enableApiFailure(int $failureType) {
        $this->failOnRequest = $failureType;
    }

    public function getTableData(string $tableName, int $page = null, int $itemsPerPage = null)
    {
        $this->requestCount += 1;
        if ($page !== null && $itemsPerPage !== null) {
            $items = $this->responseData[$tableName];
            $paginatedContent = array_slice($items, ($page - 1) * $itemsPerPage, $itemsPerPage);
            return new GuzzleResponse([$tableName => $paginatedContent], [], $this->getStatusCode());
        }
        return new GuzzleResponse([
            $tableName => $this->responseData[$tableName]
        ], [], $this->getStatusCode());
    }

    private function getStatusCode(): int {
        switch ($this->failOnRequest) {
            case self::FAIL_FIRST:
                return $this->requestCount === 1 ? 500 : 200;
            case self::FAIL_LAST:
                return $this->requestCount === count(self::TABLES) ? 500 : 200;
            case self::FAIL_RANDOM:
                // 60 % chance of failure on request
                return random_int(0, 100) > 60 ? 500 : 200;
            default:
                return 200;
        }
    }
}
