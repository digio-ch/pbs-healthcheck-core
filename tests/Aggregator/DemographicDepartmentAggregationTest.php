<?php

namespace App\Tests\Aggregator;

use App\DataFixtures\Aggregator\DemographicDepartmentAggregatorTestFixtures;
use App\Entity\WidgetDemographicDepartment;
use App\Repository\WidgetDemographicDepartmentRepository;
use App\Service\Aggregator\DepartmentDemographicAggregator;
use App\Tests\AggregatorTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class DemographicDepartmentAggregationTest extends AggregatorTestCase
{
    /**
     * @var DepartmentDemographicAggregator
     */
    private $aggregator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public static function setUpBeforeClass(string $group = null)
    {
        parent::setUpBeforeClass(DemographicDepartmentAggregatorTestFixtures::AGGREGATOR_NAME);
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->em = $container->get('doctrine')->getManager();
        $this->aggregator = $container->get(DepartmentDemographicAggregator::class);
    }

    public function testAggregate()
    {
        $this->aggregator->aggregate(new \DateTime('2020-01-01'));
        /** @var WidgetDemographicDepartmentRepository $repository */
        $repository = $this->em->getRepository(WidgetDemographicDepartment::class);
        foreach ($this->getExpectedResults() as $date => $birthyearData) {
            foreach ($birthyearData as $year => $groupData) {
                foreach ($groupData as $name => $values) {
                    $item = $repository->findOneBy([
                        'groupType' => $name,
                        'birthyear' => $year,
                        'dataPointDate' => new DateTimeImmutable($date)
                    ]);
                    $message = '[' . $date . ']: ' . $name . ' ' . $year;
                    $this->assertEquals($values['m_count'], $item->getMCount(), $message . ' m_count');
                    $this->assertEquals($values['m_leader_count'], $item->getMCountLeader(), $message . ' m_leader_count');
                    $this->assertEquals($values['f_count'], $item->getFCount(), $message . ' f_count');
                    $this->assertEquals($values['f_leader_count'], $item->getFCountLeader(), $message . ' f_leader_count');
                }
            }
        }
    }

    private function getExpectedResults()
    {
        return [
            '2020-02-01' => [
                '1990' => [
                    'Group::Woelfe' => [
                        'm_count' => 1,
                        'f_count' => 1,
                        'm_leader_count' => 0,
                        'f_leader_count' => 0
                    ]
                ],
                '1991' => [
                    'Group::Woelfe' => [
                        'm_count' => 0,
                        'f_count' => 0,
                        'm_leader_count' => 1,
                        'f_leader_count' => 1
                    ]
                ],
            ],
            '2020-03-01' => [
                '1990' => [
                    'Group::Woelfe' => [
                        'm_count' => 1,
                        'f_count' => 1,
                        'm_leader_count' => 0,
                        'f_leader_count' => 0
                    ]
                ],
                '1991' => [
                    'Group::Woelfe' => [
                        'm_count' => 1,
                        'f_count' => 1,
                        'm_leader_count' => 1,
                        'f_leader_count' => 1
                    ]
                ]
            ],
            '2020-04-01' => [
                '1990' => [
                    'Group::Woelfe' => [
                        'm_count' => 1,
                        'f_count' => 1,
                        'm_leader_count' => 0,
                        'f_leader_count' => 0
                    ]
                ],
                '1991' => [
                    'Group::Woelfe' => [
                        'm_count' => 0,
                        'f_count' => 0,
                        'm_leader_count' => 1,
                        'f_leader_count' => 1
                    ]
                ]
            ],
        ];
    }
}
