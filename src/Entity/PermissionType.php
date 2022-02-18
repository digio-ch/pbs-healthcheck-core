<?php

namespace App\Entity;

use App\Repository\PermissionTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_permission_type")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass=PermissionTypeRepository::class)
 */
class PermissionType
{
    public const OWNER = 1;
    public const EDITOR = 2;
    public const VIEWER = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $nameDe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $nameFr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $nameIt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getNameDe(): string
    {
        return $this->nameDe;
    }

    /**
     * @param string $nameDe
     */
    public function setNameDe(string $nameDe): void
    {
        $this->nameDe = $nameDe;
    }

    /**
     * @return string
     */
    public function getNameFr(): string
    {
        return $this->nameFr;
    }

    /**
     * @param string $nameFr
     */
    public function setNameFr(string $nameFr): void
    {
        $this->nameFr = $nameFr;
    }

    /**
     * @return string
     */
    public function getNameIt(): string
    {
        return $this->nameIt;
    }

    /**
     * @param string $nameIt
     */
    public function setNameIt(string $nameIt): void
    {
        $this->nameIt = $nameIt;
    }
}
