<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use App\Repository\Midata\PersonEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person_event')]
#[ORM\Entity(repositoryClass: PersonEventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PersonEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'persons')]
    private ?Event $event = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'events')]
    private ?Person $person = null;

    /***
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?string $qualified = null;

    #[ORM\JoinTable(name: 'midata_person_event_person_event_type')]
    #[ORM\JoinColumn(name: 'person_event_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'person_event_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: PersonEventType::class)]
    private $personEventTypes;

    /**
     * PersonEvent constructor.
     */
    public function __construct()
    {
        $this->personEventTypes = new ArrayCollection();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    /**
     * @param Event|null $event
     */
    public function setEvent(?Event $event): void
    {
        $this->event = $event;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setQualified(?string $qualified): void
    {
        $this->qualified = $qualified;
    }

    public function getQualified(): ?string
    {
        return $this->qualified;
    }

    /**
     * @return Collection<int, PersonEventType>
     */
    public function getPersonEventTypes(): ArrayCollection
    {
        return $this->personEventTypes;
    }

    public function addPersonEventType(PersonEventType $personEventType): void
    {
        if (!$this->personEventTypes->contains($personEventType)) {
            $this->personEventTypes[] = $personEventType;
        }
    }

    public function removePersonEventType(PersonEventType $personEventType): void
    {
        if ($this->personEventTypes->contains($personEventType)) {
            $this->personEventTypes->removeElement($personEventType);
        }
    }
}
