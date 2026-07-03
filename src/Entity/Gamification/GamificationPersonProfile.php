<?php

namespace App\Entity\Gamification;

use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\Person;
use App\Repository\Gamification\GamificationPersonProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_gamification_person_profile')]
#[ORM\Entity(repositoryClass: GamificationPersonProfileRepository::class)]
class GamificationPersonProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OneToOne(inversedBy: 'gamification', targetEntity: Person::class)]
    private ?Person $person = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Level::class)]
    private ?Level $level = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $has_used_card_layer = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $has_used_datafilter = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $has_used_timefilter = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $has_shared_el = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $access_granted_count = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $el_filled_out = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $el_revised = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $el_irrelevant = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $el_improved = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $beta_status = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): self
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

    public function getHasUsedCardLayer(): ?bool
    {
        return $this->has_used_card_layer;
    }

    public function setHasUsedCardLayer(bool $has_used_card_layer): self
    {
        $this->has_used_card_layer = $has_used_card_layer;

        return $this;
    }

    public function getHasUsedDatafilter(): ?bool
    {
        return $this->has_used_datafilter;
    }

    public function setHasUsedDatafilter(bool $has_used_datafilter): self
    {
        $this->has_used_datafilter = $has_used_datafilter;

        return $this;
    }

    public function getHasUsedTimefilter(): ?bool
    {
        return $this->has_used_timefilter;
    }

    public function setHasUsedTimefilter(bool $has_used_timefilter): self
    {
        $this->has_used_timefilter = $has_used_timefilter;

        return $this;
    }

    public function getHasSharedEl(): ?bool
    {
        return $this->has_shared_el;
    }

    public function setHasSharedEl(bool $has_shared_el): self
    {
        $this->has_shared_el = $has_shared_el;

        return $this;
    }

    public function getAccessGrantedCount(): ?int
    {
        return $this->access_granted_count;
    }

    public function setAccessGrantedCount(int $access_granted_count): self
    {
        $this->access_granted_count = $access_granted_count;

        return $this;
    }

    public function getElFilledOut(): ?bool
    {
        return $this->el_filled_out;
    }

    public function setElFilledOut(bool $el_filled_out): self
    {
        $this->el_filled_out = $el_filled_out;

        return $this;
    }

    public function getElRevised(): ?bool
    {
        return $this->el_revised;
    }

    public function setElRevised(bool $el_revised): self
    {
        $this->el_revised = $el_revised;

        return $this;
    }

    public function getElIrrelevant(): ?bool
    {
        return $this->el_irrelevant;
    }

    public function setElIrrelevant(bool $el_irrelevant): self
    {
        $this->el_irrelevant = $el_irrelevant;

        return $this;
    }

    public function getElImproved(): ?bool
    {
        return $this->el_improved;
    }

    public function setElImproved(bool $el_improved): self
    {
        $this->el_improved = $el_improved;

        return $this;
    }

    public function getBetaStatus(): ?bool
    {
        return $this->beta_status;
    }

    public function setBetaStatus(bool $beta_status): self
    {
        $this->beta_status = $beta_status;

        return $this;
    }
}
