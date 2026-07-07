<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_event_type_qualification_type')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
class EventTypeQualificationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'event_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: EventType::class, inversedBy: "eventTypeQualificationTypes")]
    private ?EventType $eventType = null;

    #[ORM\JoinColumn(name: 'qualification_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: QualificationType::class)]
    private ?QualificationType $qualificationType = null;

    /***
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private ?string $category = null;

    /***
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private ?string $role = null;

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }

    public function setEventType(?EventType $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getQualificationType(): ?QualificationType
    {
        return $this->qualificationType;
    }

    public function setQualificationType(?QualificationType $qualificationType): void
    {
        $this->qualificationType = $qualificationType;
    }
}
