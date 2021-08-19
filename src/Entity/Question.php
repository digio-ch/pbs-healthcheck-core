<?php


namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * Class Question
 * @package App\Entity
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 * @ORM\Table(name="quap_question", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="question_local_id",
 *          columns={
 *              "local_id", "aspect_id", "deleted_at"
 *          }
 *     )
 * })
 */
class Question
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $question_de
     */
    private $question_de;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $question_fr
     */
    private $question_fr;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $question_it
     */
    private $question_it;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $answer_options
     */
    private $answer_options;

    /**
     * @ORM\OneToMany(targetEntity = "Help", mappedBy = "question")
     */
    private $help;

    /**
     * @ORM\ManyToOne(targetEntity = "Aspect", inversedBy = "question")
     * @var Aspect $aspect
     */
    private $aspect;

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
        $this->help = new ArrayCollection();
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
    public function getQuestionDe(): string
    {
        return $this->question_de;
    }

    /**
     * @param string $question_de
     */
    public function setQuestionDe(string $question_de): void
    {
        $this->question_de = $question_de;
    }

    /**
     * @return string
     */
    public function getQuestionFr(): string
    {
        return $this->question_fr;
    }

    /**
     * @param string $question_fr
     */
    public function setQuestionFr(string $question_fr): void
    {
        $this->question_fr = $question_fr;
    }

    /**
     * @return string
     */
    public function getQuestionIt(): string
    {
        return $this->question_it;
    }

    /**
     * @param string $question_it
     */
    public function setQuestionIt(string $question_it): void
    {
        $this->question_it = $question_it;
    }

    /**
     * @return string
     */
    public function getAnswerOptions(): string
    {
        return $this->answer_options;
    }

    /**
     * @param string $answer_options
     */
    public function setAnswerOptions(string $answer_options): void
    {
        $this->answer_options = $answer_options;
    }

    /**
     * @return Aspect
     */
    public function getAspect(): Aspect
    {
        return $this->aspect;
    }

    /**
     * @param Aspect $aspect
     */
    public function setAspect(Aspect $aspect): void
    {
        $this->aspect = $aspect;
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

    /**
     * @return Collection|null
     */
    public function getHelp(): ?Collection
    {
        return $this->help;
    }

    /**
     * @param Collection $help
     */
    public function setHelp(Collection $help): void
    {
        $this->help = $help;
    }


}