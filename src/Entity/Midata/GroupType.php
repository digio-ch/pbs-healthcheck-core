<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\GroupTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_group_type')]
#[ORM\Entity(repositoryClass: GroupTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class GroupType
{
    public const FEDERATION = 'Group::Bund';
    public const CANTON = 'Group::Kantonalverband';
    public const REGION = 'Group::Region';
    public const DEPARTMENT = 'Group::Abteilung';
    public const DEPARTMENTS_ALLOWING_HIERARCHY = [self::FEDERATION, self::CANTON, self::REGION, self::DEPARTMENT];

    public const BIBER = 'Group::Biber';
    public const WOELFE = 'Group::Woelfe';
    public const PFADI = 'Group::Pfadi';
    public const PIO = 'Group::Pio';
    public const ABTEILUNGS_ROVER = 'Group::AbteilungsRover';
    public const ROVER = 'Group::RegionaleRover';
    public const PTA = 'Group::Pta';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $deLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $itLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $frLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $groupType = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeLabel(): ?string
    {
        return $this->deLabel;
    }

    public function setDeLabel(?string $label): void
    {
        $this->deLabel = $label;
    }

    public function getItLabel(): ?string
    {
        return $this->itLabel;
    }

    public function setItLabel(?string $label): void
    {
        $this->itLabel = $label;
    }

    public function getFrLabel(): ?string
    {
        return $this->frLabel;
    }

    public function setFrLabel(?string $label): void
    {
        $this->frLabel = $label;
    }

    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    public function setGroupType(?string $groupType): void
    {
        $this->groupType = $groupType;
    }
}
