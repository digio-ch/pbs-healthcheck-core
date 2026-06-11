<?php

namespace App\Entity\Gamification;

use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Entity\Quap\Questionnaire;
use App\Repository\Gamification\GamificationQuapEventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_gamification_quap_event")
 * @ORM\Entity(repositoryClass=GamificationQuapEventRepository::class)
 */
class GamificationQuapEvent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="gamificationQuapEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="gamificationQuapEvents")
     * @ORM\JoinColumn(nullable=true)
     */
    private $group;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private int $aspect_local_id;

    /**
     * @ORM\ManyToOne(targetEntity=Questionnaire::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $questionnaire;

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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAspectLocalId(): int
    {
        return $this->aspect_local_id;
    }

    public function setAspectLocalId(int $aspect_local_id): self
    {
        $this->aspect_local_id = $aspect_local_id;

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }
}
