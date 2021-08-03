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
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_de;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_fr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_it;

    /**
     * @ORM\OneToMany(targetEntity = "Question", mappedBy = "aspect")
     * @ORM\JoinColumn
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity = "Questionnaire", inversedBy = "aspect")
     * @ORM\JoinColumn
     */
    private $questionnaire;

    public function __construct()
    {
        $this->questionnaire = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameDe(): ?string
    {
        return $this->name_de;
    }

    public function setNameDe(string $name_de): self
    {
        $this->name_de = $name_de;

        return $this;
    }

    public function getNameFr(): ?string
    {
        return $this->name_fr;
    }

    public function setNameFr(string $name_fr): self
    {
        $this->name_fr = $name_fr;

        return $this;
    }

    public function getNameIt(): ?string
    {
        return $this->name_it;
    }

    public function setNameIt(string $name_it): self
    {
        $this->name_it = $name_it;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question): void
    {
        $this->question = $question;
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



}
