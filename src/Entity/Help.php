<?php

namespace App\Entity;

use App\Repository\HelpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Help
 * @package App\Entity
 * @ORM\Entity(repositoryClass=HelpRepository::class)
 * @ORM\Table(name = "quap_help", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="help_local_id",
 *          columns={
 *              "severity", "question_id"
 *          }
 *     )
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Help
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @var string $help_de
     */
    private $help_de;

    /**
     * @ORM\Column(type="text")
     * @var string $help_fr
     */
    private $help_fr;

    /**
     * @ORM\Column(type="text")
     * @var string $help_it
     */
    private $help_it;

    /**
     * @ORM\Column(type="integer")
     * @var int $severity
     */
    private $severity;

    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy="help")
     * @var Question $question
     */
    private $question;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=Link::class, mappedBy="helpDe", orphanRemoval=true)
     */
    private $linksDe;

    /**
     * @ORM\OneToMany(targetEntity=Link::class, mappedBy="helpFr", orphanRemoval=true)
     */
    private $linksFr;

    /**
     * @ORM\OneToMany(targetEntity=Link::class, mappedBy="helpIt", orphanRemoval=true)
     */
    private $linksIt;

    public function __construct()
    {
        $this->question = new ArrayCollection();
        $this->linksDe = new ArrayCollection();
        $this->linksFr = new ArrayCollection();
        $this->linksIt = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getHelpDe(): string
    {
        return $this->help_de;
    }

    /**
     * @param string $help_de
     */
    public function setHelpDe(string $help_de): void
    {
        $this->help_de = $help_de;
    }

    /**
     * @return string
     */
    public function getHelpFr(): string
    {
        return $this->help_fr;
    }

    /**
     * @param string $help_fr
     */
    public function setHelpFr(string $help_fr): void
    {
        $this->help_fr = $help_fr;
    }

    /**
     * @return string
     */
    public function getHelpIt(): string
    {
        return $this->help_it;
    }

    /**
     * @param string $help_it
     */
    public function setHelpIt(string $help_it): void
    {
        $this->help_it = $help_it;
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

    /**
     * @param int $severity
     */
    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
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
     * @return Collection|Link[]
     */
    public function getLinksDe(): Collection
    {
        return $this->linksDe;
    }

    /**
     * @param ArrayCollection $linksDe
     */
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
     * @return Collection|Link[]
     */
    public function getLinksFr(): Collection
    {
        return $this->linksFr;
    }

    /**
     * @param ArrayCollection $linksFr
     */
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
     * @return Collection|Link[]
     */
    public function getLinksIt(): Collection
    {
        return $this->linksIt;
    }

    /**
     * @param ArrayCollection $linksIt
     */
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
