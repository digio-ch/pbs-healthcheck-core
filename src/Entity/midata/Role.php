<?php

namespace App\Entity\midata;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="midata_role", indexes={
 *     @ORM\Index(columns={"role_type"})
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Role
{
    public const LEADER_ROLES = [
        ...self::LEADER_ROLES_PTA,
        ...self::LEADER_ROLES_ROVER,
        ...self::LEADER_ROLES_WOELFE,
        ...self::LEADER_ROLES_PFADI,
        ...self::LEADER_ROLES_BIBER,
        ...self::LEADER_ROLES_PIO,
    ];

    public const DEPARTMENT_LEADER_PTA = 'Group::Abteilung::StufenleitungPta';
    public const DEPARTMENT_LEADER_ROVER = 'Group::Abteilung::StufenleitungRover';
    public const DEPARTMENT_LEADER_PIO = 'Group::Abteilung::StufenleitungPio';
    public const DEPARTMENT_LEADER_PFADI = 'Group::Abteilung::StufenleitungPfadi';
    public const DEPARTMENT_LEADER_WOELFE = 'Group::Abteilung::StufenleitungWoelfe';
    public const DEPARTMENT_LEADER_BIBER = 'Group::Abteilung::StufenleitungBiber';

    public const DEPARTMENT_LEADER_ROLES = [
        self::DEPARTMENT_LEADER_PTA,
        self::DEPARTMENT_LEADER_ROVER,
        self::DEPARTMENT_LEADER_PIO,
        self::DEPARTMENT_LEADER_PFADI,
        self::DEPARTMENT_LEADER_WOELFE,
        self::DEPARTMENT_LEADER_BIBER,
    ];

    public const DEPARTMENT_PRESIDENT_ROLES = [
        'Group::Abteilung::Abteilungsleitung',
        'Group::Abteilung::AbteilungsleitungStv',
    ];

    public const DEPARTMENT_COACH = 'Group::Abteilung::Coach';

    public const LEADER_ROLES_PTA = [
        self::DEPARTMENT_LEADER_PTA,
        'Group::Pta::Einheitsleitung',
        'Group::Pta::Mitleitung',
    ];

    public const LEADER_ROLES_ROVER = [
        self::DEPARTMENT_LEADER_ROVER,
        'Group::AbteilungsRover::Einheitsleitung',
        'Group::AbteilungsRover::Mitleitung',
    ];

    public const LEADER_ROLES_WOELFE = [
        self::DEPARTMENT_LEADER_WOELFE,
        'Group::Woelfe::Einheitsleitung',
        'Group::Woelfe::Mitleitung',
    ];

    public const LEADER_ROLES_PFADI = [
        self::DEPARTMENT_LEADER_PFADI,
        'Group::Pfadi::Einheitsleitung',
        'Group::Pfadi::Mitleitung',
    ];

    public const LEADER_ROLES_BIBER = [
        self::DEPARTMENT_LEADER_BIBER,
        'Group::Biber::Einheitsleitung',
        'Group::Biber::Mitleitung',
    ];

    public const LEADER_ROLES_PIO = [
        self::DEPARTMENT_LEADER_PIO,
        'Group::Pio::Einheitsleitung',
        'Group::Pio::Mitleitung',
    ];

    public const PFADI_LEITPFADI = 'Group::Pfadi::Leitpfadi';

    public const PARENTS_COUNCIL_MEMBER = 'Group::Elternrat::Mitglied';
    public const PARENTS_COUNCIL_PRESIDENT = 'Group::Elternrat::Praesidium';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $layerType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roleType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupType;

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
    public function getLayerType(): ?string
    {
        return $this->layerType;
    }

    /**
     * @param null|string $type
     */
    public function setLayerType(?string $type)
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getRoleType(): ?string
    {
        return $this->roleType;
    }

    /**
     * @param null|string $roleType
     */
    public function setRoleType(?string $roleType)
    {
        $this->roleType = $roleType;
    }

    /**
     * @return null|string
     */
    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    /**
     * @param null|string $groupType
     */
    public function setGroupType(?string $groupType)
    {
        $this->groupType = $groupType;
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
