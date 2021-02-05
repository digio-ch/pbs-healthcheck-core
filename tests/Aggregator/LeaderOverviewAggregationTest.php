<?php

namespace App\Tests\Aggregator;

use App\DataFixtures\Aggregator\LeaderOverviewAggregatorTestFixtures;
use App\Entity\LeaderOverviewLeader;
use App\Entity\LeaderOverviewQualification;
use App\Entity\WidgetLeaderOverview;
use App\Repository\WidgetLeaderOverviewRepository;
use App\Service\Aggregator\LeaderOverviewAggregator;
use App\Tests\AggregatorTestCase;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class LeaderOverviewAggregationTest extends AggregatorTestCase
{
    /**
     * @var LeaderOverviewAggregator
     */
    private $aggregator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public static function setUpBeforeClass(string $group = null)
    {
        parent::setUpBeforeClass(LeaderOverviewAggregatorTestFixtures::AGGREGATOR_NAME);
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->em = $container->get('doctrine')->getManager();
        $this->aggregator = $container->get(LeaderOverviewAggregator::class);
    }

    public function testAggregate()
    {
        $this->aggregator->aggregate(new DateTime('2020-01-01'));
        /** @var WidgetLeaderOverviewRepository $repository */
        $repository = $this->em->getRepository(WidgetLeaderOverview::class);
        foreach ($this->getExpectedResults() as $date => $groups) {
            foreach ($groups as $groupName => $leader) {
                $leaderOverview = $repository->findOneBy([
                    'dataPointDate' => new DateTimeImmutable($date),
                    'groupType' => $groupName
                 ]);

                $this->em->refresh($leaderOverview);
                /** @var LeaderOverviewLeader $leaderOverviewLeader */
                $leaderOverviewLeader = $leaderOverview->getLeaders()->first();

                $this->em->refresh($leaderOverviewLeader);
                $this->assertQualifications($leaderOverviewLeader->getQualifications(), $leader['qualifications']);
            }
        }
    }

    /**
     * @param Collection $qualifications
     * @param array $expectedQualifications
     */
    private function assertQualifications(Collection $qualifications, array $expectedQualifications)
    {
        foreach ($expectedQualifications as $expectedQualification) {
            $q = $qualifications->filter(function (LeaderOverviewQualification $item) use ($expectedQualification) {
                return $item->getQualificationType()->getId() === $expectedQualification['type_id'];
            })->first();
            $this->assertNotFalse($q);
            $this->assertEquals($expectedQualification['state'], $q->getState());
            $this->assertEquals($expectedQualification['expires_at'], $q->getExpiresAt()->format('Y-m-d'));
        }
    }

    private function getExpectedResults()
    {
        $qualifications = [
            [
                'state' => 'valid',
                'expires_at' => '2021-12-31',
                'full_name' => 'J+S Leiter LS/T Jugendsport',
                'short_name' => 'JS',
                'type_id' => 23
            ],
            [
                'state' => 'valid',
                'expires_at' => '2021-12-31',
                'full_name' => 'J+S Leiter LS/T Kindersport',
                'short_name' => 'KS',
                'type_id' => 24
            ]
        ];
        return [
            '2020-02-01' => [
                'Group::Woelfe' => [
                    'qualifications' => $qualifications
                ],
                'Group::Abteilung' => [
                    'qualifications' => [
                        [
                            'state' => 'valid',
                            'expires_at' => '2021-12-31',
                            'full_name' => 'J+S Leiter LS/T Jugendsport',
                            'short_name' => 'JS',
                            'type_id' => 23
                        ],
                        [
                            'state' => 'expiring_soon',
                            'expires_at' => '2020-12-31',
                            'full_name' => 'J+S Leiter LS/T Kindersport',
                            'short_name' => 'KS',
                            'type_id' => 24
                        ],
                        [
                            'state' => 'valid',
                            'expires_at' => '2021-12-31',
                            'full_name' => 'J+S Leiter mit Zusatz im Sicherheitsbereich Berg',
                            'short_name' => 'Be',
                            'type_id' => 25
                        ]
                    ]
                ]
            ]
        ];
    }
}
