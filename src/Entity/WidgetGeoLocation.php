<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_widget_geo_location", indexes={
 *     @ORM\Index(columns={"longitude"}),
 *     @ORM\Index(columns={"latitude"}),
 *     @ORM\Index(columns={"label"}),
 *     @ORM\Index(columns={"shape"}),
 *     @ORM\Index(columns={"group_type"}),
 *     @ORM\Index(columns={"person_type"}),
 *     @ORM\Index(columns={"data_point_date"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\WidgetGeoLocationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WidgetGeoLocation extends Widget
{
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="string")
     *
     * default value 'circle'
     */
    private $shape = 'circle';

    /**
     * @ORM\Column(type="string")
     */
    private $groupType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $personType;

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
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
     * @return float|null
     */
    public function getLatitude(): ?float
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
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getShape(): ?string
    {
        return $this->shape;
    }

    /**
     * @param string $shape
     */
    public function setShape(string $shape): void
    {
        $this->shape = $shape;
    }

    /**
     * @return string
     */
    public function getGroupType(): string
    {
        return $this->groupType;
    }

    /**
     * @param string $groupType
     */
    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    /**
     * @return string|null
     */
    public function getPersonType(): ?string
    {
        return $this->personType;
    }

    /**
     * @param string|null $personType
     */
    public function setPersonType(?string $personType): void
    {
        $this->personType = $personType;
    }
}
