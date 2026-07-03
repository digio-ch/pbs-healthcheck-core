<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\CensusGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CensusGroupRepository::class)]
class CensusGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: GroupType::class)]
    private ?GroupType $group_type = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $total_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $total_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $total_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $leiter_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $leiter_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $biber_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $biber_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $woelfe_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $woelfe_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $pfadis_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $pfadis_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $pios_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $pios_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $rover_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $rover_f_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $pta_m_count = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $pta_f_count = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $group_id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $year = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupType(): ?GroupType
    {
        return $this->group_type;
    }

    /**
     * @param mixed $group_type
     */
    public function setGroupType(?GroupType $group_type): void
    {
        $this->group_type = $group_type;
    }

    public function getTotalCount(): ?int
    {
        return $this->total_count;
    }

    /**
     * @param mixed $total_count
     */
    public function setTotalCount(?int $total_count): void
    {
        $this->total_count = $total_count;
    }

    public function getTotalMCount(): ?int
    {
        return $this->total_m_count;
    }

    /**
     * @param mixed $total_m_count
     */
    public function setTotalMCount(?int $total_m_count): void
    {
        $this->total_m_count = $total_m_count;
    }

    public function getTotalFCount(): ?int
    {
        return $this->total_f_count;
    }

    /**
     * @param mixed $total_f_count
     */
    public function setTotalFCount(?int $total_f_count): void
    {
        $this->total_f_count = $total_f_count;
    }

    public function getLeiterMCount(): ?int
    {
        return $this->leiter_m_count;
    }

    /**
     * @param mixed $leiter_m_count
     */
    public function setLeiterMCount(?int $leiter_m_count): void
    {
        $this->leiter_m_count = $leiter_m_count;
    }

    public function getLeiterFCount(): ?int
    {
        return $this->leiter_f_count;
    }

    /**
     * @param mixed $leiter_f_count
     */
    public function setLeiterFCount(?int $leiter_f_count): void
    {
        $this->leiter_f_count = $leiter_f_count;
    }

    public function getBiberMCount(): ?int
    {
        return $this->biber_m_count;
    }

    /**
     * @param mixed $biber_m_count
     */
    public function setBiberMCount(?int $biber_m_count): void
    {
        $this->biber_m_count = $biber_m_count;
    }

    public function getBiberFCount(): ?int
    {
        return $this->biber_f_count;
    }

    /**
     * @param mixed $biber_f_count
     */
    public function setBiberFCount(?int $biber_f_count): void
    {
        $this->biber_f_count = $biber_f_count;
    }

    public function getWoelfeMCount(): ?int
    {
        return $this->woelfe_m_count;
    }

    /**
     * @param mixed $woelfe_m_count
     */
    public function setWoelfeMCount(?int $woelfe_m_count): void
    {
        $this->woelfe_m_count = $woelfe_m_count;
    }

    public function getWoelfeFCount(): ?int
    {
        return $this->woelfe_f_count;
    }

    /**
     * @param mixed $woelfe_f_count
     */
    public function setWoelfeFCount(?int $woelfe_f_count): void
    {
        $this->woelfe_f_count = $woelfe_f_count;
    }

    public function getPfadisMCount(): ?int
    {
        return $this->pfadis_m_count;
    }

    /**
     * @param mixed $pfadis_m_count
     */
    public function setPfadisMCount(?int $pfadis_m_count): void
    {
        $this->pfadis_m_count = $pfadis_m_count;
    }

    public function getPfadisFCount(): ?int
    {
        return $this->pfadis_f_count;
    }

    /**
     * @param mixed $pfadis_f_count
     */
    public function setPfadisFCount(?int $pfadis_f_count): void
    {
        $this->pfadis_f_count = $pfadis_f_count;
    }

    public function getPiosMCount(): ?int
    {
        return $this->pios_m_count;
    }

    /**
     * @param mixed $pios_m_count
     */
    public function setPiosMCount(?int $pios_m_count): void
    {
        $this->pios_m_count = $pios_m_count;
    }

    public function getPiosFCount(): ?int
    {
        return $this->pios_f_count;
    }

    /**
     * @param mixed $pios_f_count
     */
    public function setPiosFCount(?int $pios_f_count): void
    {
        $this->pios_f_count = $pios_f_count;
    }

    public function getRoverMCount(): ?int
    {
        return $this->rover_m_count;
    }

    /**
     * @param mixed $rover_m_count
     */
    public function setRoverMCount(?int $rover_m_count): void
    {
        $this->rover_m_count = $rover_m_count;
    }

    public function getRoverFCount(): ?int
    {
        return $this->rover_f_count;
    }

    /**
     * @param mixed $rover_f_count
     */
    public function setRoverFCount(?int $rover_f_count): void
    {
        $this->rover_f_count = $rover_f_count;
    }

    public function getPtaMCount(): ?int
    {
        return $this->pta_m_count;
    }

    /**
     * @param mixed $pta_m_count
     */
    public function setPtaMCount(?int $pta_m_count): void
    {
        $this->pta_m_count = $pta_m_count;
    }

    public function getPtaFCount(): ?int
    {
        return $this->pta_f_count;
    }

    /**
     * @param mixed $pta_f_count
     */
    public function setPtaFCount(?int $pta_f_count): void
    {
        $this->pta_f_count = $pta_f_count;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getGroupId(): ?int
    {
        return $this->group_id;
    }

    /**
     * @param mixed $group_id
     */
    public function setGroupId(?int $group_id): void
    {
        $this->group_id = $group_id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear(?string $year): void
    {
        $this->year = $year;
    }

    public function getCalculatedTotal(): int
    {
        $total = 0;
        $total += $this->getPiosMCount();
        $total += $this->getPiosFCount();
        $total += $this->getPtaMCount();
        $total += $this->getPtaFCount();
        $total += $this->getBiberMCount();
        $total += $this->getBiberFCount();
        $total += $this->getWoelfeMCount();
        $total += $this->getWoelfeFCount();
        $total += $this->getRoverMCount();
        $total += $this->getRoverFCount();
        $total += $this->getLeiterMCount();
        $total += $this->getLeiterFCount();
        $total += $this->getPfadisMCount();
        return $total + $this->getPfadisFCount();
    }
}
