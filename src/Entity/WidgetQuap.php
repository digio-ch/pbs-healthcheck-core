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
     *
     */
    private $questionnaire;

    /**
     * @ORM\Column(type = "json")
     */
    private $answers;

    public function __construct() {
        $this->questionnaire = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getQuestionnaire()
    {
        return $this->questionnaire;
    }

    /**
     * @param mixed $questionnaire
     */
    public function setQuestionnaire($questionnaire): void
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


}