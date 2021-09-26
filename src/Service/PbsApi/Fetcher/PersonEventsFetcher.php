<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Event;
use App\Entity\Person;
use App\Entity\PersonEvent;
use App\Repository\PersonEventRepository;
use App\Repository\PersonRepository;
use App\Service\PbsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

class PersonEventsFetcher extends AbstractFetcher
{
    /**
     * @var PersonEventRepository
     */
    private $personEventRepository;
    /**
     * @var Event
     */
    private $event;
    /**
     * @var PersonRepository
     */
    private $personRepository;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, PersonRepository $personRepository, Event $event) {
        parent::__construct($em, $pbsApiService);
        $this->personEventRepository = $this->em->getRepository(PersonEvent::class);
        $this->event = $event;
        $this->personRepository = $personRepository;
    }

    public function fetch(string $groupId, string $accessToken): array
    {
        $personEvents = $this->pbsApiService->getApiData('/groups/'.$groupId.'/events/'.$this->event->getId().'/participations', $accessToken);
        return $this->mapJsonToPersonEvents($personEvents, $this->event);
    }

    private function mapJsonToPersonEvents(array $json, Event $event): array
    {
        $personEventsJson = $json['event_participations'] ?? [];
        $linked = $json['linked'] ?? [];

        $personEvents = [];
        foreach ($personEventsJson as $personEventJson) {
            $personEvent = $this->personEventRepository->findOneBy(['id' => $personEventJson['id']]);
            if (!$personEvent) {
                $personEvent = new PersonEvent();
                $personEvent->setId($personEventJson['id']);
                $metadata = $this->em->getClassMetaData(get_class($personEvent));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $personEvent->setQualified($personEventJson['qualified'] ?? null);

            $personEvent->setEvent($event);

            /** @var Person $person */
            $person = $this->personRepository->findOneBy(['id' => $personEventJson['links']['person']]);
            $personEvent->setPerson($person);

            $personEvents[] = $personEvent;
        }

        return $personEvents;
    }

    public function clean(string $groupId) {
        $this->personEventRepository
            ->createQueryBuilder('pe')
            ->delete(PersonEvent::class, 'pe')
            // TODO add layer_id during import
            //->where('pe.layer_id = :layer_id')
            //->setParameter('layer_id', $groupId)
            ->getQuery()
            ->execute();
        $this->em->flush();
    }
}
