<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\PersonQualificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person_qualification')]
#[ORM\Index(columns: ['start_at'])]
#[ORM\Index(columns: ['end_at'])]
#[ORM\Entity(repositoryClass: PersonQualificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PersonQualification
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'qualification_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: QualificationType::class)]
    private ?QualificationType $qualificationType = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'qualifications')]
    private ?Person $person = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $eventOrigin = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $startAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $endAt = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventOrigin(): ?string
    {
        return $this->eventOrigin;
    }

    public function setEventOrigin(?string $eventOrigin): void
    {
        $this->eventOrigin = $eventOrigin;
    }

    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTimeImmutable $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setQualificationType(?QualificationType $qualificationType): void
    {
        $this->qualificationType = $qualificationType;
    }

    public function getQualificationType(): ?QualificationType
    {
        return $this->qualificationType;
    }
}
