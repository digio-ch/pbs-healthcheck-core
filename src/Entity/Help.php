<?php


namespace App\Entity;

use App\Repository\HelpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Help
 * @package App\Entity
 * @ORM\Entity(repositoryClass=HelpRepository::class)
 * @ORM\Table(name = "quap_help", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="help_local_id",
 *          columns={
 *              "local_id", "question_id", "deleted_at"
 *          }
 *     )
 * })
 */
class Help
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $help_de;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $help_fr;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $help_it;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $severity;

    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy="help")
     * @ORM\JoinColumn
     */
    private $question;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $local_id;

    public function __construct()
    {
        $this->question = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getHelpDe()
    {
        return $this->help_de;
    }

    /**
     * @param mixed $help_de
     */
    public function setHelpDe($help_de): void
    {
        $this->help_de = $help_de;
    }

    /**
     * @return mixed
     */
    public function getHelpFr()
    {
        return $this->help_fr;
    }

    /**
     * @param mixed $help_fr
     */
    public function setHelpFr($help_fr): void
    {
        $this->help_fr = $help_fr;
    }

    /**
     * @return mixed
     */
    public function getHelpIt()
    {
        return $this->help_it;
    }

    /**
     * @param mixed $help_it
     */
    public function setHelpIt($help_it): void
    {
        $this->help_it = $help_it;
    }

    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param mixed $severity
     */
    public function setSeverity($severity): void
    {
        $this->severity = $severity;
    }

    /**
     * @return mixed
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param mixed $help
     */
    public function setHelp($help): void
    {
        $this->help = $help;
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getLocalId(): ?int
    {
        return $this->local_id;
    }

    public function setLocalId(int $local_id): self
    {
        $this->local_id = $local_id;

        return $this;
    }




}