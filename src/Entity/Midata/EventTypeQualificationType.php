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
    #[ORM\GeneratedValue]
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
