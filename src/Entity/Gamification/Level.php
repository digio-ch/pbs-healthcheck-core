<?php

namespace App\Entity\Gamification;

use Doctrine\DBAL\Types\Types;
use App\Repository\Gamification\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_gamification_level')]
#[ORM\Entity(repositoryClass: LevelRepository::class)]
class Level
{
    public const USER = 0;
    public const GROUP = 1;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: LevelAccess::class)]
    private ?LevelAccess $access = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $type = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $de_title = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $fr_title = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $it_title = null;

    #[ORM\OneToMany(mappedBy: 'level', targetEntity: Goal::class)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private $goals;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $key = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $next_key = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $required = null;

    public function getRequired(): int
    {
        return $this->required;
    }

    /**
     * @param int $required
     */
    public function setRequired(?int $required): void
    {
        $this->required = $required;
    }

    public function getNextKey(): ?int
    {
        return $this->next_key;
    }

    /**
     * @param mixed $next_key
     */
    public function setNextKey(?int $next_key): void
    {
        $this->next_key = $next_key;
    }


    public function __construct()
    {
        $this->goals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccess(): ?LevelAccess
    {
        return $this->access;
    }

    public function setAccess(?LevelAccess $access): void
    {
        $this->access = $access;
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
        // set the owning side to null (unless already changed)
        if ($this->goals->removeElement($goal) && $goal->getLevel() === $this) {
            $goal->setLevel(null);
        }

        return $this;
    }

    public function getKey(): ?int
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey(?int $key): void
    {
        $this->key = $key;
    }
}
