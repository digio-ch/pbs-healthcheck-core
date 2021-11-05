<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @var string $type
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="WidgetQuap", mappedBy="questionnaire")
     */
    private $widgetQuap;

    /**
     * @ORM\OneToMany(targetEntity="Aspect", mappedBy="questionnaire", cascade={"persist"})
     */
    private $aspects;

    public function __construct()
    {
        $this->widgetQuap = new ArrayCollection();
        $this->aspects = new ArrayCollection();
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collection|null
     */
    public function getAspects(): ?Collection
    {
        return $this->aspects;
    }

    /**
     * @param Collection $aspects
     */
    public function setAspects(Collection $aspects): void
    {
        $this->aspects = $aspects;
    }
}
