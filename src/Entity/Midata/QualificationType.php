<?php

namespace App\Entity\Midata;

use App\Repository\Midata\QualificationTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_qualification_type")
 * @ORM\Entity(repositoryClass=QualificationTypeRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
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

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $validity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frLabel;

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
     * @return null|string
     */
    public function getDeLabel(): ?string
    {
        return $this->deLabel;
    }

    /**
     * @param null|string $label
     */
    public function setDeLabel(?string $label)
    {
        $this->deLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getItLabel(): ?string
    {
        return $this->itLabel;
    }

    /**
     * @param null|string $label
     */
    public function setItLabel(?string $label)
    {
        $this->itLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getFrLabel(): ?string
    {
        return $this->frLabel;
    }

    /**
     * @param null|string $label
     */
    public function setFrLabel(?string $label)
    {
        $this->frLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getValidity(): ?string
    {
        return $this->validity;
    }

    /**
     * @param null|string $validity
     */
    public function setValidity(?string $validity)
    {
        $this->validity = $validity;
    }
}
