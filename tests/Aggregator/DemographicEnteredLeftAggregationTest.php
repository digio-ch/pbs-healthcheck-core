<?php

namespace App\Tests\Aggregator;

use App\DataFixtures\Aggregator\DemographicEnteredLeftAggregatorTestFixtures;
use App\Entity\aggregated\AggregatedDemographicEnteredLeft;
use App\Repository\WidgetDemographicEnteredLeftRepository;
use App\Service\Aggregator\DemographicEnteredLeftAggregator;
use App\Tests\AggregatorTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class DemographicEnteredLeftAggregationTest extends AggregatorTestCase
{
    /**
     * @var DemographicEnteredLeftAggregator
     */
    private $aggregator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public static function setUpBeforeClass(string $group = null)
    {
        parent::setUpBeforeClass(DemographicEnteredLeftAggregatorTestFixtures::AGGREGATOR_NAME);
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->em = $container->get('doctrine')->getManager();
        $this->aggregator = $container->get(DemographicEnteredLeftAggregator::class);
    }

    public function testAggregate()
    {
        $this->aggregator->aggregate(new \DateTime('2020-01-01'));
        /** @var WidgetDemographicEnteredLeftRepository $repository */
        $repository = $this->em->getRepository(AggregatedDemographicEnteredLeft::class);
        foreach ($this->getExpectedResults() as $date => $values) {
            foreach ($values as $name => $data) {
                $item = $repository->findOneBy([
                    'dataPointDate' => new DateTimeImmutable($date),
                    'groupType' => $name
                ]);
                $message = '[' . $date . ']: ' . $name;
                $this->assertEquals($data['newCount'], $item->getNewCountM(), $message . ' newCountM');
                $this->assertEquals($data['newCountLeader'], $item->getNewCountLeaderM(), $message . ' newCountLeaderM');
                $this->assertEquals($data['exitCount'], $item->getExitCountM(), $message . ' exitCountM');
                $this->assertEquals($data['exitCountLeader'], $item->getExitCountLeaderM(), $message . ' exitCountLeaderM');
                $this->assertEquals($data['newCount'], $item->getNewCountF(), $message . ' newCountF');
                $this->assertEquals($data['newCountLeader'], $item->getNewCountLeaderF(), $message . ' newCountLeaderF');
                $this->assertEquals($data['exitCount'], $item->getExitCountF(), $message . ' exitCountF');
                $this->assertEquals($data['exitCountLeader'], $item->getExitCountLeaderF(), $message . ' exitCountLeaderF');
            }
        }
    }

    private function getExpectedResults()
    {
        return [
            '2020-02-01' => [
                'Group::Woelfe' => [
                    'newCount' => 2,
                    'newCountLeader' => 0,
                    'exitCount' => 0,
                    'exitCountLeader' => 0
                ]
            ],
            '2020-03-01' => [
                'Group::Woelfe' => [
                    'newCount' => 0,
                    'newCountLeader' => 2,
                    'exitCount' => 1,
                    'exitCountLeader' => 0
                ]
            ],
            '2020-04-01' => [
                'Group::Woelfe' => [
                    'newCount' => 0,
                    'newCountLeader' => 0,
                    'exitCount' => 0,
                    'exitCountLeader' => 1
                ]
            ]
        ];
    }
}
