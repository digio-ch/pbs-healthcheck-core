<?php

namespace App\Entity\Gamification;

use App\Repository\Gamification\LevelAccessRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LevelAccessRepository::class)
 */
class LevelAccess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $de_description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $fr_description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $it_description;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDeDescription(): string
    {
        return $this->de_description;
    }

    public function setDeDescription(string $de_description): void
    {
        $this->de_description = $de_description;
    }

    public function getFrDescription(): string
    {
        return $this->fr_description;
    }

    public function setFrDescription(string $fr_description): void
    {
        $this->fr_description = $fr_description;
    }

    public function getItDescription(): string
    {
        return $this->it_description;
    }

    public function setItDescription(string $it_description): void
    {
        $this->it_description = $it_description;
    }


}
