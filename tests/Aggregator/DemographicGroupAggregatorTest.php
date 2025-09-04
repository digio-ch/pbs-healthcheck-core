<?php

namespace App\Tests\Aggregator;

use App\DataFixtures\Aggregator\DemographicGroupAggregatorTestFixtures;
use App\Entity\Aggregated\AggregatedDemographicGroup;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Service\Aggregator\DemographicGroupAggregator;
use App\Tests\AggregatorTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class DemographicGroupAggregatorTest extends AggregatorTestCase
{
    /**
     * @var DemographicGroupAggregator
     */
    private $aggregator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public static function setUpBeforeClass(string $group = null)
    {
        parent::setUpBeforeClass(DemographicGroupAggregatorTestFixtures::AGGREGATOR_NAME);
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->em = $container->get('doctrine')->getManager();
        $this->aggregator = $container->get(DemographicGroupAggregator::class);
    }

    public function testAggregate()
    {
        $this->aggregator->aggregate(new \DateTime('2020-01-01'));
        /** @var AggregatedDemographicGroupRepository $repository */
        $repository = $this->em->getRepository(AggregatedDemographicGroup::class);
        foreach ($this->getExpectedResult() as $date => $values) {
            $item = $repository->findOneBy([
               'dataPointDate' => new DateTimeImmutable($date),
               'groupType' => 'Group::Woelfe'
            ]);
            $this->assertEquals($values['m'], $item->getMCount(), 'male count for ' . $date);
            $this->assertEquals($values['lm'], $item->getMCountLeader(), 'male leader count' . $date);
            $this->assertEquals($values['f'], $item->getFCount(), 'female count ' . $date);
            $this->assertEquals($values['lf'], $item->getFCountLeader(), 'female leader count ' . $date);
            $this->assertEquals($values['u'], $item->getUCount(), 'unknown count' . $date);
            $this->assertEquals($values['lu'], $item->getUCountLeader(), 'unknown leader count' . $date);
        }
    }

    private function getExpectedResult()
    {
        return $res = [
            '2020-02-01' => [
                'm' => 1,
                'lm' => 0,
                'f' => 0,
                'lf' => 0,
                'u' => 0,
                'lu' => 0
            ],
            '2020-03-01' => [
                'm' => 1,
                'lm' => 0,
                'f' => 1,
                'lf' => 0,
                'u' => 0,
                'lu' => 0
            ],
            '2020-04-01' => [
                'm' => 1,
                'lm' => 0,
                'f' => 1,
                'lf' => 0,
                'u' => 1,
                'lu' => 0
            ],
            '2020-05-01' => [
                'm' => 1,
                'lm' => 1,
                'f' => 1,
                'lf' => 0,
                'u' => 1,
                'lu' => 0
            ],
            '2020-06-01' => [
                'm' => 2,
                'lm' => 1,
                'f' => 1,
                'lf' => 1,
                'u' => 1,
                'lu' => 0
            ],
            '2020-07-01' => [
                'm' => 1,
                'lm' => 1,
                'f' => 1,
                'lf' => 1,
                'u' => 1,
                'lu' => 1
            ],
        ];
    }
}
