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
    #[ORM\GeneratedValue]
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

    #[ORM\OneToMany(mappedBy: 'geoAddress', targetEntity: Person::class)]
    private $people;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getZip(): int
    {
        return $this->zip;
    }

    /**
     * @param int $zip
     */
    public function setZip(int $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getTown(): string
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getHouse(): string
    {
        return $this->house;
    }

    /**
     * @param string $house
     */
    public function setHouse(string $house): void
    {
        $this->house = $house;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
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
