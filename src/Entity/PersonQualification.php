<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_person_qualification", indexes={
 *     @ORM\Index(columns={"start_at"}),
 *     @ORM\Index(columns={"end_at"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PersonQualificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PersonQualification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="QualificationType")
     * @ORM\JoinColumn(name="qualification_type_id", referencedColumnName="id")
     */
    private $qualificationType;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="qualifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     **/
    private $eventOrigin;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endAt;

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
    public function getEventOrigin(): ?string
    {
        return $this->eventOrigin;
    }

    /**
     * @param null|string $eventOrigin
     */
    public function setEventOrigin(?string $eventOrigin)
    {
        $this->eventOrigin = $eventOrigin;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    /**
     * @param DateTimeImmutable|null $startAt
     */
    public function setStartAt(?DateTimeImmutable $startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    /**
     * @param DateTimeImmutable|null $endAt
     */
    public function setEndAt(?DateTimeImmutable $endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @param Person|null $person
     */
    public function setPerson(?Person $person)
    {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param QualificationType|null $qualificationType
     */
    public function setQualificationType(?QualificationType $qualificationType)
    {
        $this->qualificationType = $qualificationType;
    }

    /**
     * @return mixed
     */
    public function getQualificationType()
    {
        return $this->qualificationType;
    }
}
