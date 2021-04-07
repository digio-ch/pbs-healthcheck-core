<?php


namespace App\Service\Aggregator;


use DateTime;

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
