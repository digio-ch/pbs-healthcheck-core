<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\Person;
use App\Entity\PersonEvent;
use App\Repository\PersonEventRepository;
use App\Repository\PersonRepository;
use App\Service\PbsApiService;
use Doctrine\ORM\EntityManagerInterface;

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

    public function fetch(Group $syncGroup, string $accessToken): array
    {
        $groupId = $syncGroup->getMidataId();
        $personEvents = $this->pbsApiService->getApiData('/groups/'.$groupId.'/events/'.$this->event->getMidataId().'/participations', $accessToken);
        return $this->mapJsonToPersonEvents($personEvents, $this->event, $syncGroup);
    }

    private function mapJsonToPersonEvents(array $json, Event $event, Group $syncGroup): array
    {
        $personEventsJson = $json['event_participations'] ?? [];
        $linked = $json['linked'] ?? [];

        $personEvents = [];
        foreach ($personEventsJson as $personEventJson) {
            $personEvent = $this->personEventRepository->findOneBy(['midataId' => $personEventJson['id'], 'syncGroup' => $syncGroup]);
            if (!$personEvent) {
                $personEvent = new PersonEvent();
                $personEvent->setMidataId($personEventJson['id']);
                $personEvent->setSyncGroup($syncGroup);
            }
            $personEvent->setQualified($personEventJson['qualified'] ?? null);

            $personEvent->setEvent($event);

            /** @var Person $person */
            $person = $this->personRepository->findOneBy(['midataId' => $personEventJson['links']['person'], 'syncGroup' => $syncGroup]);
            $personEvent->setPerson($person);

            $personEvents[] = $personEvent;
        }

        return $personEvents;
    }

    public function clean(string $groupId) {
        $this->personEventRepository
            ->createQueryBuilder('pe')
            ->delete(PersonEvent::class, 'pe')
            ->where('pe.syncGroup = :sync_group_id')
            ->setParameter('sync_group_id', $groupId)
            ->getQuery()
            ->execute();
        $this->em->flush();
    }
}
