<?php

namespace App\Entity;

use App\Repository\GeoLocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_geo_location")
 * @ORM\Entity(repositoryClass=GeoLocationRepository::class)
 */
class GeoLocation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="geoLocations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $abteilung;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getAbteilung(): ?Group
    {
        return $this->abteilung;
    }

    public function setAbteilung(?Group $abteilung): self
    {
        $this->abteilung = $abteilung;

        return $this;
    }
}
