<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionnaireRepository::class)
 * @ORM\Table(name="quap_questionnaire")
 */
class Questionnaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity = "WidgetQuap", mappedBy = "questionnaire")
     *
     */
    private $widgetQuap;

    /**
     * @ORM\OneToMany(targetEntity = "Aspect", mappedBy = "questionnaire")
     * @ORM\JoinColumn
     */
    private $aspect;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidgetQuap()
    {
        return $this->widgetQuap;
    }

    /**
     * @param mixed $widgetQuap
     */
    public function setWidgetQuap($widgetQuap): void
    {
        $this->widgetQuap = $widgetQuap;
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



}
