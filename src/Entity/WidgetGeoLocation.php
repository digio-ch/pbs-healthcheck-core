<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_widget_geo_location", indexes={
 *     @ORM\Index(columns={"longitude"}),
 *     @ORM\Index(columns={"latitude"}),
 *     @ORM\Index(columns={"label"}),
 *     @ORM\Index(columns={"shape"}),
 *     @ORM\Index(columns={"color"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\WidgetGeoLocationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WidgetGeoLocation extends Widget
{
    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", nullable="true")
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
     *
     * default color (same gray as leader)
     */
    private $color = '#929292';

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
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
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
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}
