<?php

namespace App\Entity\Quap;

use Doctrine\DBAL\Types\Types;
use App\Repository\Quap\AspectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_quap_aspect')]
#[ORM\UniqueConstraint(name: 'aspect_local_id', columns: ['local_id', 'questionnaire_id'])]
#[ORM\Entity(repositoryClass: AspectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Aspect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $local_id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name_de = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name_fr = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name_it = null;

    #[ORM\OneToMany(mappedBy: 'aspect', targetEntity: Question::class, cascade: ['persist'])]
    private $questions;

    #[ORM\ManyToOne(targetEntity: Questionnaire::class, inversedBy: 'aspects')]
    private ?Questionnaire $questionnaire = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $deletedAt;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionDe = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionFr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionIt = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNameDe(): string
    {
        return $this->name_de;
    }

    public function setNameDe(string $name_de): void
    {
        $this->name_de = $name_de;
    }

    public function getNameFr(): string
    {
        return $this->name_fr;
    }

    public function setNameFr(string $name_fr): void
    {
        $this->name_fr = $name_fr;
    }

    public function getNameIt(): string
    {
        return $this->name_it;
    }

    public function setNameIt(string $name_it): void
    {
        $this->name_it = $name_it;
    }

    public function getQuestionnaire(): Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(Questionnaire $questionnaire): void
    {
        $this->questionnaire = $questionnaire;
    }

    public function getLocalId(): ?int
    {
        return $this->local_id;
    }

    /**
     * @return $this
     */
    public function setLocalId(int $local_id): self
    {
        $this->local_id = $local_id;

        return $this;
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
     * @return Collection<int, Question>
     */
    public function getQuestions(): ?Collection
    {
        return $this->questions;
    }

    public function setQuestions(Collection $questions): void
    {
        $this->questions = $questions;
    }

    public function getDescriptionDe(): ?string
    {
        return $this->descriptionDe;
    }

    public function setDescriptionDe(string $descriptionDe): self
    {
        $this->descriptionDe = $descriptionDe;

        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(string $descriptionFr): self
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    public function getDescriptionIt(): ?string
    {
        return $this->descriptionIt;
    }

    public function setDescriptionIt(string $descriptionIt): self
    {
        $this->descriptionIt = $descriptionIt;

        return $this;
    }
}
