<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Event;
use App\Entity\EventDate;
use App\Entity\PersonRole;
use App\Repository\EventDateRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

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
    public function mapFromJson(array $dateJson, Event $event): ?EventDate
    {
        $eventDate = $this->eventDateRepository->findOneBy(['id' => $dateJson['id']]);
        if (!$eventDate) {
            $eventDate = new EventDate();
            $eventDate->setId($dateJson['id']);
            $metadata = $this->em->getClassMetaData(get_class($eventDate));
            $metadata->setIdGenerator(new AssignedGenerator());
        }
        $eventDate->setEvent($event);

        $eventDate->setStartAt(new DateTimeImmutable($dateJson['start_at']));
        if ($dateJson['finish_at']) {
            $eventDate->setEndAt(new DateTimeImmutable($dateJson['finish_at']));
        }

        return $eventDate;
    }
}
