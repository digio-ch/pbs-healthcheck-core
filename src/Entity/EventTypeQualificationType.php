<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_event_type_qualification_type")
 * @ORM\Entity(repositoryClass="App\Repository\EventTypeQualificationTypeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EventTypeQualificationType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(name="event_type_id", referencedColumnName="id")
     */
    private $eventType;

    /**
     * @ORM\ManyToOne(targetEntity="QualificationTYpe")
     * @ORM\JoinColumn(name="qualification_type_id", referencedColumnName="id")
     */
    private $qualificationType;

    /***
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $category;

    /***
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $role;

    /**
     * @return null|string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param null|string $category
     */
    public function setCategory(?string $category)
    {
        $this->category = $category;
    }

    /**
     * @return null|string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param null|string $role
     */
    public function setRole(?string $role)
    {
        $this->role = $role;
    }

    /**
     * @return EventType|null
     */
    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }

    /**
     * @param EventType|null $eventType
     */
    public function setEventType(?EventType $eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return QualificationType|null
     */
    public function getQualificationType(): ?QualificationType
    {
        return $this->qualificationType;
    }

    /**
     * @param QualificationType|null $qualificationType
     */
    public function setQualificationType(?QualificationType $qualificationType)
    {
        $this->qualificationType = $qualificationType;
    }
}
