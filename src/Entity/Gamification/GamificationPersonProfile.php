<?php

namespace App\Entity\Gamification;

use App\Entity\Midata\Person;
use App\Repository\Gamification\GamificationPersonProfileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GamificationPersonProfileRepository::class)
 */
class GamificationPersonProfile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Person::class, inversedBy="gamification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity=Level::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_used_card_layer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_used_datafilter;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_used_timefilter;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_shared_el;

    /**
     * @ORM\Column(type="integer")
     */
    private $access_granted_count;

    /**
     * @ORM\Column(type="boolean")
     */
    private $el_filled_out;

    /**
     * @ORM\Column(type="boolean")
     */
    private $el_revised;

    /**
     * @ORM\Column(type="boolean")
     */
    private $el_irrelevant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $el_improved;


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
}
