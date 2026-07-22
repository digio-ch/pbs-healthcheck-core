<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Widgets;

class LeaderDTO
{
    private ?string $name = null;

    private string $birthday;

    private ?string $gender = null;

    /**
     * @var QualificationDTO[]
     */
    private array $qualifications = [];

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }

    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return QualificationDTO[]
     */
    public function getQualifications(): array
    {
        return $this->qualifications;
    }

    public function addQualification(QualificationDTO $qualification): void
    {
        $this->qualifications[] = $qualification;
    }
}
