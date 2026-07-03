<?php

namespace App\Entity\Gamification;

use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use App\Entity\Midata\Person;
use App\Repository\Gamification\LevelUpLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_gamification_level_up_log')]
#[ORM\Entity(repositoryClass: LevelUpLogRepository::class)]
class LevelUpLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'levelUps')]
    private ?Person $person = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Level::class)]
    private ?Level $level = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private $date;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => false])]
    private ?bool $displayed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDisplayed(): ?bool
    {
        return $this->displayed;
    }

    public function setDisplayed(bool $displayed): self
    {
        $this->displayed = $displayed;

        return $this;
    }
}
