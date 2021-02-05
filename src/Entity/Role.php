<?php

namespace App\Entity;

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
