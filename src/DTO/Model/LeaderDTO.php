<?php

namespace App\DTO\Model;

class LeaderDTO
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var QualificationDTO[]
     */
    private $qualifications = [];

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string|null $gender
     */
    public function setGender(?string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return QualificationDTO[]
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }

    public function addQualification(QualificationDTO $qualification)
    {
        $this->qualifications[] = $qualification;
    }
}
