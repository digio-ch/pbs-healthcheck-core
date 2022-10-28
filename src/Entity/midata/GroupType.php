<?php

namespace App\Entity\midata;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_group_type")
 * @ORM\Entity(repositoryClass="App\Repository\GroupTypeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class GroupType
{
    public const FEDERATION = 1;
    public const CANTON = 2;
    public const REGION = 3;
    public const DEPARTMENT = 7;

    public const BIBER = 8;
    public const WOELFE = 9;
    public const PFADI = 10;
    public const PIO = 11;
    public const ROVER = 12;
    public const PTA = 13;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupType;

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
}
