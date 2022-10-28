<?php

namespace App\Tests\Aggregator;

use App\DataFixtures\Aggregator\DemographicCampAggregatorTestFixtures;
use App\Entity\aggregated\AggregatedDemographicCampGroup;
use App\Entity\aggregated\AggregatedDemographicCamp;
use App\Repository\WidgetDemographicCampRepository;
use App\Service\Aggregator\DemographicCampAggregator;
use App\Tests\AggregatorTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class DemographicCampAggregatorTest extends AggregatorTestCase
{
    /**
     * @var DemographicCampAggregator
     */
    private $aggregator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public static function setUpBeforeClass(string $group = null)
    {
        parent::setUpBeforeClass(DemographicCampAggregatorTestFixtures::AGGREGATOR_NAME);
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->em = $container->get('doctrine')->getManager();
        $this->aggregator = $container->get(DemographicCampAggregator::class);
    }

    public function testAggregate()
    {
        $this->aggregator->aggregate(new \DateTime('2020-01-01'));
        /** @var WidgetDemographicCampRepository $repository */
        $repository = $this->em->getRepository(AggregatedDemographicCamp::class);
        foreach ($this->getExpectedResult() as $date => $events) {
            foreach ($events as $event) {
                $camp = $repository->findOneBy([
                    'dataPointDate' => new DateTimeImmutable($date),
                    'campName' => $event['camp_name'],
                    'startDate' => new DateTimeImmutable($event['start_date'])
                ]);
                if ($event['state'] === 'canceled') {
                    $this->assertNull($camp);
                    continue;
                }
                $this->assertNotNull($camp);
                $this->em->refresh($camp);

                foreach ($event['groups'] as $group) {
                    $campGroup = $this->findGroupForId($camp, $group['group_id'], $group['group_type']);
                    $this->assertNotFalse($campGroup);
                    $this->assertEquals($group['m_count'], $campGroup->getMCount());
                    $this->assertEquals($group['f_count'], $campGroup->getFCount());
                    $this->assertEquals($group['m_count_leader'], $campGroup->getMCountLeader());
                    $this->assertEquals($group['f_count_leader'], $campGroup->getFCountLeader());
                }

            }
        }
    }

    private function findGroupForId(AggregatedDemographicCamp $camp, int $id, string $groupType)
    {
        /** @var AggregatedDemographicCampGroup $dcg */
        foreach ($camp->getDemographicCampGroups()->getValues() as $dcg) {
            if ($dcg->getGroup()->getId() === $id && $dcg->getGroupType() === $groupType) {
                return $dcg;
            }
        }
        return false;
    }

    private function getExpectedResult()
    {
        return [
            '2020-02-01' => [
                [
                    'camp_name' => 'Abteilung Event',
                    'start_date' => '2020-01-28',
                    'state' => 'canceled',
                    'groups' => [
                        [
                            'group_id' => 301,
                            'group_type' => 'Group::Woelfe',
                            'm_count' => 3,
                            'f_count' => 0,
                            'm_count_leader' => 1,
                            'f_count_leader' => 0,
                        ],
                    ]
                ],
                [
                    'camp_name' => 'Wolfstufe Event',
                    'start_date' => '2020-01-03',
                    'state' => 'created',
                    'groups' => [
                        [
                            'group_id' => 301,
                            'group_type' => 'Group::Woelfe',
                            'm_count' => 3,
                            'f_count' => 0,
                            'm_count_leader' => 1,
                            'f_count_leader' => 0,
                        ],
                    ]
                ],
                [
                    'camp_name' => 'Meute 1 Event',
                    'start_date' => '2020-01-07',
                    'state' => 'created',
                    'groups' => [
                        [
                            'group_id' => 301,
                            'group_type' => 'Group::Woelfe',
                            'm_count' => 3,
                            'f_count' => 0,
                            'm_count_leader' => 1,
                            'f_count_leader' => 0,
                        ],
                    ]
                ]
            ],
            '2020-03-01' => [
                [
                    'camp_name' => 'Abteilung Event',
                    'start_date' => '2020-02-10',
                    'state' => 'canceled',
                    'groups' => [
                        [
                            'group_id' => 301,
                            'group_type' => 'Group::Woelfe',
                            'm_count' => 3,
                            'f_count' => 0,
                            'm_count_leader' => 1,
                            'f_count_leader' => 0,
                        ],
                    ]
                ],
            ]
        ];
    }
}
