<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Course extends Event
{
    /**
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(name="event_type_id", referencedColumnName="id")
     */
    private $eventType;

    /**
     * @param EventType|null $eventType
     */
    public function setEventType(?EventType $eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }
}
