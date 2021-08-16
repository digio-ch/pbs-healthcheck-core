<?php


namespace App\Entity;

use App\Repository\HelpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Help
 * @package App\Entity
 * @ORM\Entity(repositoryClass=HelpRepository::class)
 * @ORM\Table(name = "quap_help", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="help_local_id",
 *          columns={
 *              "local_id", "question_id"
 *          }
 *     )
 * })
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
    private $deleted_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $local_id;

    public function __construct()
    {
        $this->question = new ArrayCollection();
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getLocalId(): ?int
    {
        return $this->local_id;
    }

    public function setLocalId(int $local_id): self
    {
        $this->local_id = $local_id;

        return $this;
    }




}