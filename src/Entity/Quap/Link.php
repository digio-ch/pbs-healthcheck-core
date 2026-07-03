<?php

namespace App\Entity\Quap;

use Doctrine\DBAL\Types\Types;
use App\Repository\Quap\LinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_quap_link')]
#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $url = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Help::class, inversedBy: 'linksDe')]
    private ?Help $helpDe = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Help::class, inversedBy: 'linksFr')]
    private ?Help $helpFr = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Help::class, inversedBy: 'linksIt')]
    private ?Help $helpIt = null;

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
