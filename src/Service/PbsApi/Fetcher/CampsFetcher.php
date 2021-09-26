<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Camp;
use App\Entity\EventDate;
use App\Entity\YouthSportType;
use App\Repository\CampRepository;
use App\Repository\EventDateRepository;
use App\Repository\PersonRepository;
use App\Repository\YouthSportTypeRepository;
use App\Service\PbsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

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

    protected function fetch(string $groupId, string $accessToken): array
    {
        $startDate = date('d-m-Y', strtotime('-10 years'));
        $endDate = date('d-m-Y', strtotime('+5 years'));
        $campData = $this->pbsApiService->getApiData('/groups/'.$groupId.'/events?type=Event::Camp&start_date='.$startDate.'&end_date='.$endDate, $accessToken);
        return $this->mapJsonToCamps($campData, $groupId, $accessToken);
    }

    private function mapJsonToCamps(array $json, string $groupId, string $accessToken): array
    {
        $campsJson = $json['events'] ?? [];
        $linked = $json['linked'] ?? [];

        $camps = [];
        foreach ($campsJson as $campJson) {
            $camp = $this->campRepository->findOneBy(['id' => $campJson['id']]);
            if (!$camp) {
                $camp = new Camp();
                $camp->setId($campJson['id']);
                $metadata = $this->em->getClassMetaData(get_class($camp));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $camp->setName($campJson['name']);
            $camp->setLocation($campJson['location']);
            $camp->setState($campJson['state']);

            /** @var YouthSportType $youthYouthType */
            $youthYouthType = $this->youthSportTypeRepository->findOneBy(['type' => $campJson['j_s_kind'] ?? 'j_s_kind_none']);
            $camp->setYouthSportType($youthYouthType);

            foreach ($campJson['links']['dates'] ?? [] as $dateId) {
                $camp->addEventDate($this->eventDateMapper->mapFromJson($this->getLinked($linked, 'event_dates', $dateId), $camp));
            }

            $personEventsFetcher = new PersonEventsFetcher($this->em, $this->pbsApiService, $this->personRepository, $camp);
            $personEvents = $personEventsFetcher->fetch($groupId, $accessToken);
            foreach ($personEvents as $personEvent) {
                $camp->addPerson($personEvent);
            }

            $camps[] = $camp;
        }

        return $camps;
    }

    public function clean(string $groupId) {
        $this->eventDateRepository
            ->createQueryBuilder('ed')
            ->delete(EventDate::class, 'ed')
            // TODO add layer_id during import
            //->where('ed.layer_id = :layer_id')
            //->setParameter('layer_id', $groupId)
            ->getQuery()
            ->execute();

        $this->campRepository
            ->createQueryBuilder('c')
            ->delete(Camp::class, 'c')
            // TODO add layer_id during import
            //->where('c.layer_id = :layer_id')
            //->setParameter('layer_id', $groupId)
            ->getQuery()
            ->execute();

        $this->em->flush();
    }
}
