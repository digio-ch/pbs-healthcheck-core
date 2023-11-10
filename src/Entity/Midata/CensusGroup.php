<?php

namespace App\Entity\Midata;

use App\Repository\Midata\CensusGroupRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CensusGroupRepository::class)
 */
class CensusGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=GroupType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_type;
     /**
      * @ORM\Column(type="integer")
      */
     private $total_count;
      /**
       * @ORM\Column(type="integer")
       */

      private $total_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $total_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $leiter_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $leiter_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $biber_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $biber_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $woelfe_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $woelfe_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $pfadis_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $pfadis_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $pios_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $pios_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $rover_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $rover_f_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $pta_m_count;
      /**
       * @ORM\Column(type="integer")
       */
      private $pta_f_count;
      /**
       * @ORM\Column(type="string", length=255)
       */
      private $name;
      /**
       * @ORM\Column(type="integer")
       */
      private $group_id;
      /**
       * @ORM\Column(type="string", length=255)
       */

    private $year;
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getGroupType()
    {
        return $this->group_type;
    }

    /**
     * @param mixed $group_type
     */
    public function setGroupType($group_type): void
    {
        $this->group_type = $group_type;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->total_count;
    }

    /**
     * @param mixed $total_count
     */
    public function setTotalCount($total_count): void
    {
        $this->total_count = $total_count;
    }

    /**
     * @return mixed
     */
    public function getTotalMCount()
    {
        return $this->total_m_count;
    }

    /**
     * @param mixed $total_m_count
     */
    public function setTotalMCount($total_m_count): void
    {
        $this->total_m_count = $total_m_count;
    }

    /**
     * @return mixed
     */
    public function getTotalFCount()
    {
        return $this->total_f_count;
    }

    /**
     * @param mixed $total_f_count
     */
    public function setTotalFCount($total_f_count): void
    {
        $this->total_f_count = $total_f_count;
    }

    /**
     * @return mixed
     */
    public function getLeiterMCount()
    {
        return $this->leiter_m_count;
    }

    /**
     * @param mixed $leiter_m_count
     */
    public function setLeiterMCount($leiter_m_count): void
    {
        $this->leiter_m_count = $leiter_m_count;
    }

    /**
     * @return mixed
     */
    public function getLeiterFCount()
    {
        return $this->leiter_f_count;
    }

    /**
     * @param mixed $leiter_f_count
     */
    public function setLeiterFCount($leiter_f_count): void
    {
        $this->leiter_f_count = $leiter_f_count;
    }

    /**
     * @return mixed
     */
    public function getBiberMCount()
    {
        return $this->biber_m_count;
    }

    /**
     * @param mixed $biber_m_count
     */
    public function setBiberMCount($biber_m_count): void
    {
        $this->biber_m_count = $biber_m_count;
    }

    /**
     * @return mixed
     */
    public function getBiberFCount()
    {
        return $this->biber_f_count;
    }

    /**
     * @param mixed $biber_f_count
     */
    public function setBiberFCount($biber_f_count): void
    {
        $this->biber_f_count = $biber_f_count;
    }

    /**
     * @return mixed
     */
    public function getWoelfeMCount()
    {
        return $this->woelfe_m_count;
    }

    /**
     * @param mixed $woelfe_m_count
     */
    public function setWoelfeMCount($woelfe_m_count): void
    {
        $this->woelfe_m_count = $woelfe_m_count;
    }

    /**
     * @return mixed
     */
    public function getWoelfeFCount()
    {
        return $this->woelfe_f_count;
    }

    /**
     * @param mixed $woelfe_f_count
     */
    public function setWoelfeFCount($woelfe_f_count): void
    {
        $this->woelfe_f_count = $woelfe_f_count;
    }

    /**
     * @return mixed
     */
    public function getPfadisMCount()
    {
        return $this->pfadis_m_count;
    }

    /**
     * @param mixed $pfadis_m_count
     */
    public function setPfadisMCount($pfadis_m_count): void
    {
        $this->pfadis_m_count = $pfadis_m_count;
    }

    /**
     * @return mixed
     */
    public function getPfadisFCount()
    {
        return $this->pfadis_f_count;
    }

    /**
     * @param mixed $pfadis_f_count
     */
    public function setPfadisFCount($pfadis_f_count): void
    {
        $this->pfadis_f_count = $pfadis_f_count;
    }

    /**
     * @return mixed
     */
    public function getPiosMCount()
    {
        return $this->pios_m_count;
    }

    /**
     * @param mixed $pios_m_count
     */
    public function setPiosMCount($pios_m_count): void
    {
        $this->pios_m_count = $pios_m_count;
    }

    /**
     * @return mixed
     */
    public function getPiosFCount()
    {
        return $this->pios_f_count;
    }

    /**
     * @param mixed $pios_f_count
     */
    public function setPiosFCount($pios_f_count): void
    {
        $this->pios_f_count = $pios_f_count;
    }

    /**
     * @return mixed
     */
    public function getRoverMCount()
    {
        return $this->rover_m_count;
    }

    /**
     * @param mixed $rover_m_count
     */
    public function setRoverMCount($rover_m_count): void
    {
        $this->rover_m_count = $rover_m_count;
    }

    /**
     * @return mixed
     */
    public function getRoverFCount()
    {
        return $this->rover_f_count;
    }

    /**
     * @param mixed $rover_f_count
     */
    public function setRoverFCount($rover_f_count): void
    {
        $this->rover_f_count = $rover_f_count;
    }

    /**
     * @return mixed
     */
    public function getPtaMCount()
    {
        return $this->pta_m_count;
    }

    /**
     * @param mixed $pta_m_count
     */
    public function setPtaMCount($pta_m_count): void
    {
        $this->pta_m_count = $pta_m_count;
    }

    /**
     * @return mixed
     */
    public function getPtaFCount()
    {
        return $this->pta_f_count;
    }

    /**
     * @param mixed $pta_f_count
     */
    public function setPtaFCount($pta_f_count): void
    {
        $this->pta_f_count = $pta_f_count;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * @param mixed $group_id
     */
    public function setGroupId($group_id): void
    {
        $this->group_id = $group_id;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
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
        $total += $this->getPfadisFCount();
        return $total;
    }
}
