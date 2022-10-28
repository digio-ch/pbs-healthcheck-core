<?php

namespace App\Entity\quap;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LinkRepository::class)
 * @ORM\Table(name="hc_quap_link")
 */
class Link
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Help::class, inversedBy="linksDe")
     * @ORM\JoinColumn(nullable=true)
     */
    private $helpDe;

    /**
     * @ORM\ManyToOne(targetEntity=Help::class, inversedBy="linksFr")
     * @ORM\JoinColumn(nullable=true)
     */
    private $helpFr;

    /**
     * @ORM\ManyToOne(targetEntity=Help::class, inversedBy="linksIt")
     * @ORM\JoinColumn(nullable=true)
     */
    private $helpIt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getHelpDe(): ?Help
    {
        return $this->helpDe;
    }

    public function setHelpDe(?Help $helpDe): self
    {
        $this->helpDe = $helpDe;

        return $this;
    }

    public function getHelpFr(): ?Help
    {
        return $this->helpFr;
    }

    public function setHelpFr(?Help $helpFr): self
    {
        $this->helpFr = $helpFr;

        return $this;
    }

    public function getHelpIt(): ?Help
    {
        return $this->helpIt;
    }

    public function setHelpIt(?Help $helpIt): self
    {
        $this->helpIt = $helpIt;

        return $this;
    }
}
