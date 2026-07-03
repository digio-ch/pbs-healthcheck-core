<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\QualificationTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_qualification_type')]
#[ORM\Entity(repositoryClass: QualificationTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class QualificationType
{
    public const ABSOLVENT_AL = 1;
    public const ABSOLVENT_EINFUEHRUNGSKURS_BIBER = 2;
    public const ABSOLVENT_EINFUEHRUNGSKURS_PIO = 3;
    public const ABSOLVENT_EINFUEHRUNGSKURS_PTA = 4;
    public const ABSOLVENT_EINFUEHRUNGSKURS_ROVER = 5;
    public const ABSOLVENT_PANORAMAKURS = 10;
    public const JS_COACH = 15;
    public const JS_LAGERLEITER = 22;
    public const JS_LEITER_JUGENDSPORT = 23;
    public const JS_LEITER_KINDERSPORT = 24;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $validity = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $deLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $itLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $frLabel = null;

    public static $qualificationTypesShortcuts = [
        1 => 'AL',
        2 => 'EkB',
        3 => 'EkP',
        4 => 'EkPTA',
        5 => 'EkR',
        7 => 'F',
        8 => 'Gi',
        9 => 'L',
        10 => 'Pa',
        11 => 'P',
        14 => 'MiK',
        22 => 'A',
        23 => 'JS',
        24 => 'KS',
        25 => 'Be',
        26 => 'Wa',
        27 => 'Wi',
    ];

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

    public function getValidity(): ?string
    {
        return $this->validity;
    }

    public function setValidity(?string $validity): void
    {
        $this->validity = $validity;
    }
}
