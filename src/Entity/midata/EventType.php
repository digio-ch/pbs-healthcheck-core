<?php

namespace App\Entity\midata;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_event_type")
 * @ORM\Entity(repositoryClass="App\Repository\EventTypeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EventType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frLabel;

    /**
     * @ORM\OneToMany(targetEntity=EventTypeQualificationType::class, mappedBy="eventType")
     */
    private $eventTypeQualificationTypes;

    /**
     * EventType constructor.
     */
    public function __construct()
    {
        $this->eventTypeQualificationTypes = new ArrayCollection();
    }


    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getDeLabel(): ?string
    {
        return $this->deLabel;
    }

    /**
     * @param null|string $label
     */
    public function setDeLabel(?string $label)
    {
        $this->deLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getItLabel(): ?string
    {
        return $this->itLabel;
    }

    /**
     * @param null|string $label
     */
    public function setItLabel(?string $label)
    {
        $this->itLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getFrLabel(): ?string
    {
        return $this->frLabel;
    }

    /**
     * @param null|string $label
     */
    public function setFrLabel(?string $label)
    {
        $this->frLabel = $label;
    }

    /**
     * @return Collection
     */
    public function getEventTypeQualificationTypes(): Collection
    {
        return $this->eventTypeQualificationTypes;
    }

    /**
     * @param Collection $eventTypeQualificationTypes
     */
    public function setEventTypeQualificationTypes(Collection $eventTypeQualificationTypes): void
    {
        $this->eventTypeQualificationTypes = $eventTypeQualificationTypes;
    }
}
