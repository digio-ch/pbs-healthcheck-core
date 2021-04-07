<?php


namespace App\Service\Aggregator;


use App\DTO\Mapper\GeoAdminLocationMapper;
use App\DTO\Model\GeoAdminLocationDTO;
use DateTime;
use GuzzleHttp\Client;

class GeoDataAggregator extends WidgetAggregator
{
    private const NAME = 'widget.geo-data';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    public function aggregate(DateTime $startDate = null)
    {
        // TODO: Implement aggregate() method.
    }
}
