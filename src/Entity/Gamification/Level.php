<?php

namespace App\Entity\Gamification;

use App\Repository\Gamification\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LevelRepository::class)
 */
class Level
{
    public const USER = 0;
    public const GROUP = 1;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $de_title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fr_title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $it_title;

    /**
     * @ORM\OneToMany(targetEntity=Goal::class, mappedBy="level")
     */
    private $goals;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $key;

    public function __construct()
    {
        $this->goals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    public function getFrTitle(): ?string
    {
        return $this->fr_title;
    }

    public function setFrTitle(string $fr_title): self
    {
        $this->fr_title = $fr_title;

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

    /**
     * @return Collection<int, Goal>
     */
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    public function addGoal(Goal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals[] = $goal;
            $goal->setLevel($this);
        }

        return $this;
    }

    public function removeGoal(Goal $goal): self
    {
        if ($this->goals->removeElement($goal)) {
            // set the owning side to null (unless already changed)
            if ($goal->getLevel() === $this) {
                $goal->setLevel(null);
            }
        }

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
