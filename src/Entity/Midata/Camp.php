<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\CampRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Camp extends Event
{
    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\JoinColumn(name: 'youth_sport_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: YouthSportType::class)]
    private ?YouthSportType $youthYouthType = null;

    /**
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param null|string $state
     */
    public function setState(?string $state)
    {
        $this->state = $state;
    }

    /**
     * @return null|string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param null|string $location
     */
    public function setLocation(?string $location)
    {
        $this->location = $location;
    }

    /**
     * @return YouthSportType|null
     */
    public function getYouthSportType(): ?YouthSportType
    {
        return $this->youthYouthType;
    }

    /**
     * @param YouthSportType|null $youthYouthType
     */
    public function setYouthSportType(?YouthSportType $youthYouthType)
    {
        $this->youthYouthType = $youthYouthType;
    }
}
