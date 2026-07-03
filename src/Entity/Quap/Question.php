<?php

namespace App\Entity\Quap;

use Doctrine\DBAL\Types\Types;
use App\Repository\Quap\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Question
 * @package App\Entity
 */
#[ORM\Table(name: 'hc_quap_question')]
#[ORM\UniqueConstraint(name: 'question_local_id', columns: ['local_id', 'aspect_id'])]
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Question
{
    const ANSWER_OPTION_BINARY = 'binary';
    const ANSWER_OPTION_MIDATA_BINARY = 'midata-binary';
    const ANSWER_OPTION_RANGE = 'range';
    const ANSWER_OPTION_MIDATA_RANGE = 'midata-range';

    const ANSWER_NOT_ANSWERED = 0;
    const ANSWER_FULLY_APPLIES = 1;
    const ANSWER_PARTIALLY_APPLIES = 2;
    const ANSWER_SOMEWHAT_APPLIES = 3;
    const ANSWER_DONT_APPLIES = 4;
    const ANSWER_NOT_RELEVANT = 5;

    /**
     * @var int $id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $local_id = null;

    /**
     * @var string $question_de
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $question_de = null;

    /**
     * @var string $question_fr
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $question_fr = null;

    /**
     * @var string $question_it
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $question_it = null;

    /**
     * @var string $answer_options
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $answer_options = null;

    /**
     * @var string|null $evaluation_function
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $evaluation_function = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Help::class, cascade: ['persist'])]
    private $help;

    /**
     * @var Aspect $aspect
     */
    #[ORM\ManyToOne(targetEntity: Aspect::class, inversedBy: 'questions')]
    private ?Aspect $aspect = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $deletedAt;

    public function __construct()
    {
        $this->help = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return string|null
     */
    public function getEvaluationFunction(): ?string
    {
        return $this->evaluation_function;
    }

    /**
     * @param string|null $evaluation_function
     */
    public function setEvaluationFunction(?string $evaluation_function): void
    {
        $this->evaluation_function = $evaluation_function;
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

    /**
     * @return Collection<int, Help>
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
