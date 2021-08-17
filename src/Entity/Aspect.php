<?php

namespace App\Entity;

use App\Repository\AspectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=AspectRepository::class)
 * @ORM\Table(
 *     name="quap_aspect",
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *          name="aspect_local_id",
 *          columns={
 *              "local_id", "questionnaire_id"
 *          }
 *     )
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Aspect
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $local_id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name_de
     */
    private $name_de;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name_fr
     */
    private $name_fr;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name_it
     */
    private $name_it;

    /**
     * @ORM\OneToMany(targetEntity = "Question", mappedBy = "aspect")
     */
    private $questions;

    /**
     * @ORM\ManyToOne(targetEntity = "Questionnaire", inversedBy = "aspect")
     * @var Questionnaire $questionnaire
     */
    private $questionnaire;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
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
    public function getNameDe(): string
    {
        return $this->name_de;
    }

    /**
     * @param string $name_de
     */
    public function setNameDe(string $name_de): void
    {
        $this->name_de = $name_de;
    }

    /**
     * @return string
     */
    public function getNameFr(): string
    {
        return $this->name_fr;
    }

    /**
     * @param string $name_fr
     */
    public function setNameFr(string $name_fr): void
    {
        $this->name_fr = $name_fr;
    }

    /**
     * @return string
     */
    public function getNameIt(): string
    {
        return $this->name_it;
    }

    /**
     * @param string $name_it
     */
    public function setNameIt(string $name_it): void
    {
        $this->name_it = $name_it;
    }

    /**
     * @return Questionnaire
     */
    public function getQuestionnaire(): Questionnaire
    {
        return $this->questionnaire;
    }

    /**
     * @param Questionnaire $questionnaire
     */
    public function setQuestionnaire(Questionnaire $questionnaire): void
    {
        $this->questionnaire = $questionnaire;
    }

    /**
     * @return int|null
     */
    public function getLocalId(): ?int
    {
        return $this->local_id;
    }

    /**
     * @param int $local_id
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
     * @return ArrayCollection
     */
    public function getQuestions(): ArrayCollection
    {
        return $this->questions;
    }
}
