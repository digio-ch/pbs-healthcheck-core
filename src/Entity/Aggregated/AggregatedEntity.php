<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\Group;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

class AggregatedEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    protected ?Group $group = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected $dataPointDate;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): void
    {
        $this->group = $group;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDataPointDate(): ?DateTimeImmutable
    {
        return $this->dataPointDate;
    }

    public function setDataPointDate(DateTimeImmutable $dataPointDate): void
    {
        $this->dataPointDate = $dataPointDate;
    }
}
