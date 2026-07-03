<?php

namespace App\Entity\Midata;

use App\Repository\Midata\CourseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Course extends Event
{
    #[ORM\JoinColumn(name: 'event_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: EventType::class)]
    private ?EventType $eventType = null;

    public function setEventType(?EventType $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }
}
