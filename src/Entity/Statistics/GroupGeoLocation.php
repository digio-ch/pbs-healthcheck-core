<?php

namespace App\Entity\Statistics;

use Doctrine\DBAL\Types\Types;
use App\Repository\Statistics\GroupGeoLocationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupGeoLocationRepository::class)]
class GroupGeoLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: StatisticGroup::class, inversedBy: 'geoLocations')]
    private ?StatisticGroup $group = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $lat = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $long = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getGroup(): ?StatisticGroup
    {
        return $this->group;
    }

    public function setGroup(?StatisticGroup $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLong(): ?string
    {
        return $this->long;
    }

    public function setLong(string $long): self
    {
        $this->long = $long;

        return $this;
    }
}
