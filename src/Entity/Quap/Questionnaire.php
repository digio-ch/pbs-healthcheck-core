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

    /**
     * @var int $id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * @var string $type
     */
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'questionnaire', targetEntity: AggregatedQuap::class)]
    private $widgetQuap;

    #[ORM\OneToMany(mappedBy: 'questionnaire', targetEntity: Aspect::class, cascade: ['persist'])]
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
     * @return Collection<int, Aspect>
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
