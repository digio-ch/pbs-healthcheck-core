<?php

namespace App\Entity;

use App\Repository\WidgetQuapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class WidgetQuap
 * @package App\Entity
 * @ORM\Entity(repositoryClass=WidgetQuapRepository::class)
 * @ORM\Table(name = "hc_widget_quap")
 */
class WidgetQuap extends Widget
{

    /**
     * @ORM\ManyToOne(targetEntity="Questionnaire", inversedBy = "widgetQuap")
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
