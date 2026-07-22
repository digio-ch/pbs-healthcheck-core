<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\EventTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_event_type')]
#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class EventType
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $deLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $itLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $frLabel = null;

    #[ORM\OneToMany(targetEntity: EventTypeQualificationType::class, mappedBy: 'eventType')]
    private $eventTypeQualificationTypes;

    /**
     * EventType constructor.
     */
    public function __construct()
    {
        $this->eventTypeQualificationTypes = new ArrayCollection();
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeLabel(): ?string
    {
        return $this->deLabel;
    }

    public function setDeLabel(?string $label): void
    {
        $this->deLabel = $label;
    }

    public function getItLabel(): ?string
    {
        return $this->itLabel;
    }

    public function setItLabel(?string $label): void
    {
        $this->itLabel = $label;
    }

    public function getFrLabel(): ?string
    {
        return $this->frLabel;
    }

    public function setFrLabel(?string $label): void
    {
        $this->frLabel = $label;
    }

    /**
     * @return Collection<int, EventTypeQualificationType>
     */
    public function getEventTypeQualificationTypes(): Collection
    {
        return $this->eventTypeQualificationTypes;
    }

    public function setEventTypeQualificationTypes(Collection $eventTypeQualificationTypes): void
    {
        $this->eventTypeQualificationTypes = $eventTypeQualificationTypes;
    }
}
