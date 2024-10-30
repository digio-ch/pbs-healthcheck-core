<?php

namespace App\Entity\Gamification;

use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Entity\Quap\Aspect;
use App\Entity\Quap\Questionnaire;
use App\Repository\Gamification\GamificationQuapEventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
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
     * When submitting QUAP answers sadly the payload includes no information about the aspect
     * that was changed. It only includes the raw information of the answers in an array, the association
     * seems to happen magically?
     * I decided to embrace that system and to log the ID in said array and not the aspect which it
     * corresponds to. This also comes with the advantage that only human changable aspects are inside that
     * array, so we don't need to filter later in the evaluation.
     * If you would like to understand what im talking about, check the request that happens
     * when you change a QUAP Aspect.
     * @ORM\Column(type="integer")
     */
    private $local_change_index;

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

    public function getLocalChangeIndex(): ?int
    {
        return $this->local_change_index;
    }

    public function setLocalChangeIndex(int $local_change_index): self
    {
        $this->local_change_index = $local_change_index;

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
