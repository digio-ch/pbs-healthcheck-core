<?php

namespace App\Entity\Security;

use Doctrine\DBAL\Types\Types;
use App\Repository\Security\PermissionTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_security_permission_type')]
#[ORM\Entity(repositoryClass: PermissionTypeRepository::class)]
class PermissionType
{
    public const EDITOR = 'editor';
    public const OWNER = 'owner';
    public const VIEWER = 'viewer';
    public const EDITOR_PLUS = 'editor-plus';

    public const OWNER_ID = 1;
    public const EDITOR_ID = 2;
    public const VIEWER_ID = 3;
    public const EDITOR_PLUS_ID = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $key;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $nameDe;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $nameFr;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $nameIt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getNameDe(): string
    {
        return $this->nameDe;
    }

    public function setNameDe(string $nameDe): void
    {
        $this->nameDe = $nameDe;
    }

    public function getNameFr(): string
    {
        return $this->nameFr;
    }

    public function setNameFr(string $nameFr): void
    {
        $this->nameFr = $nameFr;
    }

    public function getNameIt(): string
    {
        return $this->nameIt;
    }

    public function setNameIt(string $nameIt): void
    {
        $this->nameIt = $nameIt;
    }
}
