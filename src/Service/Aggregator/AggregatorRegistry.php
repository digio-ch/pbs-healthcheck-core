<?php

namespace App\Service\Aggregator;

use InvalidArgumentException;

class AggregatorRegistry
{
    /**
     * @var array
     */
    private $aggregators = [];

    public function __construct(iterable $handlers)
    {
        $this->aggregators = iterator_to_array($handlers);
    }

    /**
     * @return array
     */
    public function getAggregators(): array
    {
        return $this->aggregators;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAggregator(string $name): bool
    {
        return isset($this->aggregators[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAggregator(string $name): WidgetAggregator
    {
        if (!$this->hasAggregator($name)) {
            throw new InvalidArgumentException(sprintf('Aggregator "%s" does not exist.', $name));
        }

        return $this->aggregators[$name];
    }
}
