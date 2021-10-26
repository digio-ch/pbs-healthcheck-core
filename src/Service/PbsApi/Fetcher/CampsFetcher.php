<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Camp;
use App\Entity\EventDate;
use App\Entity\Group;
use App\Entity\YouthSportType;
use App\Repository\CampRepository;
use App\Repository\EventDateRepository;
use App\Repository\PersonRepository;
use App\Repository\YouthSportTypeRepository;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\PbsApiService;
use Doctrine\ORM\EntityManagerInterface;

class CampsFetcher extends AbstractFetcher
{
    /**
     * @var CampRepository
     */
    private $campRepository;
    /**
     * @var YouthSportTypeRepository
     */
    private $youthSportTypeRepository;
    /**
     * @var EventDateMapper
     */
    private $eventDateMapper;
    /**
     * @var PersonRepository
     */
    private $personRepository;
    /**
     * @var EventDateRepository
     */
    private $eventDateRepository;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, EventDateMapper $eventDateMapper, PersonRepository $personRepository, EventDateRepository $eventDateRepository) {
        parent::__construct($em, $pbsApiService);
        $this->campRepository = $this->em->getRepository(Camp::class);
        $this->youthSportTypeRepository = $this->em->getRepository(YouthSportType::class);
        $this->eventDateMapper = $eventDateMapper;
        $this->personRepository = $personRepository;
        $this->eventDateRepository = $eventDateRepository;
    }

    protected function fetch(Group $syncGroup, string $accessToken): array
    {
        $groupId = $syncGroup->getMidataId();
        $startDate = date('d-m-Y', strtotime(WidgetAggregator::AGGREGATION_START_DATE));
        $endDate = date('d-m-Y', strtotime('+5 years'));
        $campData = $this->pbsApiService->getApiData('/groups/'.$groupId.'/events?type=Event::Camp&start_date='.$startDate.'&end_date='.$endDate, $accessToken);
        return $this->mapJsonToCamps($campData, $syncGroup, $accessToken);
    }

    private function mapJsonToCamps(array $json, Group $syncGroup, string $accessToken): array
    {
        $campsJson = $json['events'] ?? [];
        $linked = $json['linked'] ?? [];

        $camps = [];
        foreach ($campsJson as $campJson) {
            $camp = $this->campRepository->findOneBy(['midataId' => $campJson['id'], 'syncGroup' => $syncGroup]);
            if (!$camp) {
                $camp = new Camp();
                $camp->setMidataId($campJson['id']);
                $camp->setSyncGroup($syncGroup);
            }
            $camp->setName($campJson['name']);
            $camp->setLocation($campJson['location']);
            $camp->setState($campJson['state']);

            /** @var YouthSportType $youthYouthType */
            $youthYouthType = $this->youthSportTypeRepository->findOneBy(['type' => $campJson['j_s_kind'] ?? 'j_s_kind_none']);
            $camp->setYouthSportType($youthYouthType);

            foreach ($campJson['links']['dates'] ?? [] as $dateId) {
                $camp->addEventDate($this->eventDateMapper->mapFromJson($this->getLinked($linked, 'event_dates', $dateId), $camp, $syncGroup));
            }

            $personEventsFetcher = new PersonEventsFetcher($this->em, $this->pbsApiService, $this->personRepository, $camp);
            $personEvents = $personEventsFetcher->fetch($syncGroup, $accessToken);
            foreach ($personEvents as $personEvent) {
                $camp->addPerson($personEvent);
            }

            $camps[] = $camp;
        }

        return $camps;
    }

    public function clean(string $groupId) {
        $this->em->createQueryBuilder()
            ->delete(EventDate::class, 'ed')
            ->where('ed.syncGroup = :sync_group_id')
            ->setParameter('sync_group_id', $groupId)
            ->getQuery()
            ->execute();

        $this->em->createQueryBuilder()
            ->delete(Camp::class, 'c')
            ->where('c.syncGroup = :sync_group_id')
            ->setParameter('sync_group_id', $groupId)
            ->getQuery()
            ->execute();

        $this->em->flush();
    }
}
