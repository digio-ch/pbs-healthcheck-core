<?php

namespace App\Entity\Aggregated;

use App\Entity\Quap\Questionnaire;
use App\Repository\Aggregated\AggregatedQuapRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AggregatedQuap
 * @package App\Entity
 * @ORM\Entity(repositoryClass=AggregatedQuapRepository::class)
 * @ORM\Table(name = "hc_aggregated_quap")
 */
class AggregatedQuap extends AggregatedEntity
{
    /**
     * @ORM\ManyToOne(targetEntity=Questionnaire::class, inversedBy = "widgetQuap")
     * @ORM\JoinColumn(nullable=false)
     * @var Questionnaire $questionnaire
     */
    private $questionnaire;

    /**
     * @ORM\Column(type="json")
     */
    private $answers;

    /**
     * @ORM\Column(type="json")
     */
    private $computedAnswers;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $dataPointDate;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var bool $allowAccess
     */
    private bool $allowAccess = false;

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
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param mixed $answers
     */
    public function setAnswers($answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @return mixed
     */
    public function getComputedAnswers()
    {
        return $this->computedAnswers;
    }

    /**
     * @param mixed $computedAnswers
     */
    public function setComputedAnswers($computedAnswers): void
    {
        $this->computedAnswers = $computedAnswers;
    }

    /**
     * @return bool
     */
    public function getAllowAccess(): bool
    {
        return $this->allowAccess;
    }

    /**
     * @param bool $allowAccess
     */
    public function setAllowAccess(bool $allowAccess): void
    {
        $this->allowAccess = $allowAccess;
    }
}
