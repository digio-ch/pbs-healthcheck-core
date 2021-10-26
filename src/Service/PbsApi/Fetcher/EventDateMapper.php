<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Event;
use App\Entity\EventDate;
use App\Entity\Group;
use App\Entity\PersonRole;
use App\Repository\EventDateRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class EventDateMapper
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EventDateRepository
     */
    private $eventDateRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->eventDateRepository = $this->em->getRepository(PersonRole::class);
    }

    /**
     * @param array $dateJson
     * @return PersonRole|null
     * @throws \Exception
     */
    public function mapFromJson(array $dateJson, Event $event, Group $syncGroup): ?EventDate
    {
        $eventDate = $this->eventDateRepository->findOneBy(['midataId' => $dateJson['id'], 'syncGroup' => $syncGroup]);
        if (!$eventDate) {
            $eventDate = new EventDate();
            $eventDate->setMidataId($dateJson['id']);
            $eventDate->setSyncGroup($syncGroup);
        }
        $eventDate->setEvent($event);

        $eventDate->setStartAt(new DateTimeImmutable($dateJson['start_at']));
        if ($dateJson['finish_at']) {
            $eventDate->setEndAt(new DateTimeImmutable($dateJson['finish_at']));
        }

        return $eventDate;
    }
}
