<?php

namespace App\Entity\Quap;

use Doctrine\DBAL\Types\Types;
use App\Entity\Aggregated\AggregatedQuap;
use App\Repository\Quap\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_quap_questionnaire')]
#[ORM\Entity(repositoryClass: QuestionnaireRepository::class)]
class Questionnaire
{
    public const TYPE_DEPARTMENT = 'Questionnaire::Group::Default';
    public const TYPE_CANTON = 'Questionnaire::Group::Canton';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $type = null;

    #[ORM\OneToMany(targetEntity: AggregatedQuap::class, mappedBy: 'questionnaire')]
    private $widgetQuap;

    #[ORM\OneToMany(targetEntity: Aspect::class, mappedBy: 'questionnaire', cascade: ['persist'])]
    private ?Collection $aspects = null;

    public function __construct()
    {
        $this->widgetQuap = new ArrayCollection();
        $this->aspects = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collection<int, Aspect>
     */
    public function getAspects(): ?Collection
    {
        return $this->aspects;
    }

    public function setAspects(Collection $aspects): void
    {
        $this->aspects = $aspects;
    }
}
