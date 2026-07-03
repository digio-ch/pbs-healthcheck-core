<?php

namespace App\Entity\Quap;

use Doctrine\DBAL\Types\Types;
use App\Repository\Quap\HelpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Help
 * @package App\Entity
 */
#[ORM\Table(name: 'hc_quap_help')]
#[ORM\UniqueConstraint(name: 'help_local_id', columns: ['severity', 'question_id'])]
#[ORM\Entity(repositoryClass: HelpRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Help
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $help_de = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $help_fr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $help_it = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $severity = null;

    #[ORM\ManyToOne(targetEntity: \Question::class, inversedBy: 'help')]
    private Question $question;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $deletedAt;

    #[ORM\OneToMany(mappedBy: 'helpDe', targetEntity: Link::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection $linksDe;

    #[ORM\OneToMany(mappedBy: 'helpFr', targetEntity: Link::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection $linksFr;

    #[ORM\OneToMany(mappedBy: 'helpIt', targetEntity: Link::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection $linksIt;

    public function __construct()
    {
        $this->question = new ArrayCollection();
        $this->linksDe = new ArrayCollection();
        $this->linksFr = new ArrayCollection();
        $this->linksIt = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getHelpDe(): string
    {
        return $this->help_de;
    }

    public function setHelpDe(string $help_de): void
    {
        $this->help_de = $help_de;
    }

    public function getHelpFr(): string
    {
        return $this->help_fr;
    }

    public function setHelpFr(string $help_fr): void
    {
        $this->help_fr = $help_fr;
    }

    public function getHelpIt(): string
    {
        return $this->help_it;
    }

    public function setHelpIt(string $help_it): void
    {
        $this->help_it = $help_it;
    }

    public function getSeverity(): int
    {
        return $this->severity;
    }

    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     */
    public function setDeletedAt($deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinksDe(): Collection
    {
        return $this->linksDe;
    }

    public function setLinksDe(ArrayCollection $linksDe): void
    {
        $this->linksDe = $linksDe;
    }

    public function addLinksDe(Link $linksDe): self
    {
        if (!$this->linksDe->contains($linksDe)) {
            $this->linksDe[] = $linksDe;
            $linksDe->setHelpDe($this);
        }

        return $this;
    }

    public function removeLinksDe(Link $linksDe): self
    {
        if ($this->linksDe->removeElement($linksDe)) {
            // set the owning side to null (unless already changed)
            if ($linksDe->getHelpDe() === $this) {
                $linksDe->setHelpDe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinksFr(): Collection
    {
        return $this->linksFr;
    }

    public function setLinksFr(ArrayCollection $linksFr): void
    {
        $this->linksFr = $linksFr;
    }

    public function addLinksFr(Link $linksFr): self
    {
        if (!$this->linksFr->contains($linksFr)) {
            $this->linksFr[] = $linksFr;
            $linksFr->setHelpFr($this);
        }

        return $this;
    }

    public function removeLinksFr(Link $linksFr): self
    {
        if ($this->linksFr->removeElement($linksFr)) {
            // set the owning side to null (unless already changed)
            if ($linksFr->getHelpFr() === $this) {
                $linksFr->setHelpFr(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinksIt(): Collection
    {
        return $this->linksIt;
    }

    public function setLinksIt(ArrayCollection $linksIt): void
    {
        $this->linksIt = $linksIt;
    }

    public function addLinksIt(Link $linksIt): self
    {
        if (!$this->linksIt->contains($linksIt)) {
            $this->linksIt[] = $linksIt;
            $linksIt->setHelpIt($this);
        }

        return $this;
    }

    public function removeLinksIt(Link $linksIt): self
    {
        if ($this->linksIt->removeElement($linksIt)) {
            // set the owning side to null (unless already changed)
            if ($linksIt->getHelpIt() === $this) {
                $linksIt->setHelpIt(null);
            }
        }

        return $this;
    }
}
