<?php

namespace App\Entity\Admin;

use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\Person;
use App\Repository\Admin\GeoAddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'admin_geo_address')]
#[ORM\Index(columns: ['zip'])]
#[ORM\Index(columns: ['town'])]
#[ORM\Index(columns: ['address'])]
#[ORM\Index(columns: ['house'])]
#[ORM\Entity(repositoryClass: GeoAddressRepository::class)]
class GeoAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $zip = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $town = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $house = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $longitude = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $latitude = null;

    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'geoAddress')]
    private $people;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    public function getZip(): int
    {
        return $this->zip;
    }

    public function setZip(int $zip): void
    {
        $this->zip = $zip;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getHouse(): string
    {
        return $this->house;
    }

    public function setHouse(string $house): void
    {
        $this->house = $house;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return array|GeoAddress[]
     */
    public function getPeople(): array
    {
        return $this->people->toArray();
    }
}
