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

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Group|null
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }

    /**
     * @param Group|null $group
     */
    public function setGroup(?Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDataPointDate(): ?DateTimeImmutable
    {
        return $this->dataPointDate;
    }

    /**
     * @param DateTimeImmutable $dataPointDate
     */
    public function setDataPointDate(DateTimeImmutable $dataPointDate)
    {
        $this->dataPointDate = $dataPointDate;
    }
}
