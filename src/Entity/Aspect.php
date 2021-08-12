<?php

namespace App\Entity;

use App\Repository\AspectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AspectRepository::class)
 * @ORM\Table(name="quap_aspect")
 */
class Aspect
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name_de
     */
    private $name_de;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name_fr
     */
    private $name_fr;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name_it
     */
    private $name_it;

    /**
     * @ORM\OneToMany(targetEntity = "Question", mappedBy = "aspect")
     */
    private $questions;

    /**
     * @ORM\ManyToOne(targetEntity = "Questionnaire", inversedBy = "aspect")
     * @var Questionnaire $questionnaire
     */
    private $questionnaire;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
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
    public function getNameDe(): string
    {
        return $this->name_de;
    }

    /**
     * @param string $name_de
     */
    public function setNameDe(string $name_de): void
    {
        $this->name_de = $name_de;
    }

    /**
     * @return string
     */
    public function getNameFr(): string
    {
        return $this->name_fr;
    }

    /**
     * @param string $name_fr
     */
    public function setNameFr(string $name_fr): void
    {
        $this->name_fr = $name_fr;
    }

    /**
     * @return string
     */
    public function getNameIt(): string
    {
        return $this->name_it;
    }

    /**
     * @param string $name_it
     */
    public function setNameIt(string $name_it): void
    {
        $this->name_it = $name_it;
    }

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
}
