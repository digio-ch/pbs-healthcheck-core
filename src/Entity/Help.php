<?php


namespace App\Entity;

use App\Repository\HelpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Help
 * @package App\Entity
 * @ORM\Entity(repositoryClass=HelpRepository::class)
 * @ORM\Table(name = "quap_help")
 */
class Help
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     * @var int $id
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $help_de
     */
    private $help_de;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $help_fr
     */
    private $help_fr;

    /**
     * @ORM\Column(type = "string", length = 255)
     * @var string $help_it
     */
    private $help_it;

    /**
     * @ORM\Column(type = "integer")
     * @var int $severity
     */
    private $severity;

    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy="help")
     * @var Question $question
     */
    private $question;

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
    public function getHelpDe(): string
    {
        return $this->help_de;
    }

    /**
     * @param string $help_de
     */
    public function setHelpDe(string $help_de): void
    {
        $this->help_de = $help_de;
    }

    /**
     * @return string
     */
    public function getHelpFr(): string
    {
        return $this->help_fr;
    }

    /**
     * @param string $help_fr
     */
    public function setHelpFr(string $help_fr): void
    {
        $this->help_fr = $help_fr;
    }

    /**
     * @return string
     */
    public function getHelpIt(): string
    {
        return $this->help_it;
    }

    /**
     * @param string $help_it
     */
    public function setHelpIt(string $help_it): void
    {
        $this->help_it = $help_it;
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

    /**
     * @param int $severity
     */
    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }


}