<?php


namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $question_de;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $question_fr;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $question_it;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $answer_options;

    /**
     * @ORM\OneToMany(targetEntity = "Help", mappedBy = "question")
     * @ORM\JoinColumn
     */
    private $help;

    /**
     * @ORM\ManyToOne(targetEntity = "Aspect", inversedBy = "question")
     * @ORM\JoinColumn
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
        $this->aspect = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getQuestionDe()
    {
        return $this->question_de;
    }

    /**
     * @param mixed $question_de
     */
    public function setQuestionDe($question_de): void
    {
        $this->question_de = $question_de;
    }

    /**
     * @return mixed
     */
    public function getQuestionFr()
    {
        return $this->question_fr;
    }

    /**
     * @param mixed $question_fr
     */
    public function setQuestionFr($question_fr): void
    {
        $this->question_fr = $question_fr;
    }

    /**
     * @return mixed
     */
    public function getQuestionIt()
    {
        return $this->question_it;
    }

    /**
     * @param mixed $question_it
     */
    public function setQuestionIt($question_it): void
    {
        $this->question_it = $question_it;
    }

    /**
     * @return mixed
     */
    public function getAnswerOptions()
    {
        return $this->answer_options;
    }

    /**
     * @param mixed $answer_options
     */
    public function setAnswerOptions($answer_options): void
    {
        $this->answer_options = $answer_options;
    }

    /**
     * @return mixed
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param mixed $help
     */
    public function setHelp($help): void
    {
        $this->help = $help;
    }

    /**
     * @return mixed
     */
    public function getAspect()
    {
        return $this->aspect;
    }

    /**
     * @param mixed $aspect
     */
    public function setAspect($aspect): void
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


}