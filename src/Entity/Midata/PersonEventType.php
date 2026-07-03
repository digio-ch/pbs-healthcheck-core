<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\PersonEventTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person_event_type')]
#[ORM\Entity(repositoryClass: PersonEventTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PersonEventType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $deLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $itLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $frLabel = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
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
}
