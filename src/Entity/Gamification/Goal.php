<?php

namespace App\Entity\Gamification;

use App\Repository\Gamification\GoalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoalRepository::class)
 */
class Goal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="goals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(type="boolean")
     */
    private $required;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $de_title;

    /**
     * @ORM\Column(type="text")
     */
    private $de_information;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $de_help;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fr_title;

    /**
     * @ORM\Column(type="text")
     */
    private $fr_information;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fr_help;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $it_title;

    /**
     * @ORM\Column(type="text")
     */
    private $it_information;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $it_help;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $key;



    public function getId(): ?int
    {
        return $this->id;
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

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getDeTitle(): ?string
    {
        return $this->de_title;
    }

    public function setDeTitle(string $de_title): self
    {
        $this->de_title = $de_title;

        return $this;
    }

    public function getDeInformation(): ?string
    {
        return $this->de_information;
    }

    public function setDeInformation(string $de_information): self
    {
        $this->de_information = $de_information;

        return $this;
    }

    public function getDeHelp(): ?string
    {
        return $this->de_help;
    }

    public function setDeHelp(?string $de_help): self
    {
        $this->de_help = $de_help;

        return $this;
    }

    public function getFrTitle(): ?string
    {
        return $this->fr_title;
    }

    public function setFrTitle(string $fr_title): self
    {
        $this->fr_title = $fr_title;

        return $this;
    }

    public function getFrInformation(): ?string
    {
        return $this->fr_information;
    }

    public function setFrInformation(string $fr_information): self
    {
        $this->fr_information = $fr_information;

        return $this;
    }

    public function getFrHelp(): ?string
    {
        return $this->fr_help;
    }

    public function setFrHelp(?string $fr_help): self
    {
        $this->fr_help = $fr_help;

        return $this;
    }

    public function getItTitle(): ?string
    {
        return $this->it_title;
    }

    public function setItTitle(string $it_title): self
    {
        $this->it_title = $it_title;

        return $this;
    }

    public function getItInformation(): ?string
    {
        return $this->it_information;
    }

    public function setItInformation(string $it_information): self
    {
        $this->it_information = $it_information;

        return $this;
    }

    public function getItHelp(): ?string
    {
        return $this->it_help;
    }

    public function setItHelp(?string $it_help): self
    {
        $this->it_help = $it_help;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
