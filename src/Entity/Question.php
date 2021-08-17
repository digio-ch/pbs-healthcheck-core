<?php


namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
 *              "local_id", "aspect_id"
 *          }
 *     )
 * })
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="integer")
     */
    private $local_id;

    /**
     * @ORM\Column(type="text")
     * @var string $question_de
     */
    private $question_de;

    /**
     * @ORM\Column(type="text")
     * @var string $question_fr
     */
    private $question_fr;

    /**
     * @ORM\Column(type="text")
     * @var string $question_it
     */
    private $question_it;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $answer_options
     */
    private $answer_options;

    /**
     * @ORM\OneToMany(targetEntity="Help", mappedBy="question")
     */
    private $help;

    /**
     * @ORM\ManyToOne(targetEntity="Aspect", inversedBy="question")
     * @var Aspect $aspect
     */
    private $aspect;

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
}

    /**
     * @return PersistentCollection
     */
    public function getHelp(): PersistentCollection
    {
        return $this->help;
    }
}