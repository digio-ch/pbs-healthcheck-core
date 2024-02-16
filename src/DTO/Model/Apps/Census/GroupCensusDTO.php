<?php

namespace App\DTO\Model\Apps\Census;

class GroupCensusDTO
{
    private string $year;
    private int $total_m_count;
    private int $total_f_count;
    private int $leiter_m_count;
    private int $leiter_f_count;
    private int $biber_m_count;
    private int $biber_f_count;
    private int $woelfe_m_count;
    private int $woelfe_f_count;
    private int $pfadis_m_count;
    private int $pfadis_f_count;
    private int $pios_m_count;
    private int $pios_f_count;
    private int $rover_m_count;
    private int $rover_f_count;
    private int $pta_m_count;
    private int $pta_f_count;

    /**
     * @return string
     */
    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear(string $year): void
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getTotalMCount(): int
    {
        return $this->total_m_count;
    }

    /**
     * @param int $total_m_count
     */
    public function setTotalMCount(int $total_m_count): void
    {
        $this->total_m_count = $total_m_count;
    }

    /**
     * @return int
     */
    public function getTotalFCount(): int
    {
        return $this->total_f_count;
    }

    /**
     * @param int $total_f_count
     */
    public function setTotalFCount(int $total_f_count): void
    {
        $this->total_f_count = $total_f_count;
    }

    /**
     * @return int
     */
    public function getLeiterMCount(): int
    {
        return $this->leiter_m_count;
    }

    /**
     * @param int $leiter_m_count
     */
    public function setLeiterMCount(int $leiter_m_count): void
    {
        $this->leiter_m_count = $leiter_m_count;
    }

    /**
     * @return int
     */
    public function getLeiterFCount(): int
    {
        return $this->leiter_f_count;
    }

    /**
     * @param int $leiter_f_count
     */
    public function setLeiterFCount(int $leiter_f_count): void
    {
        $this->leiter_f_count = $leiter_f_count;
    }

    /**
     * @return int
     */
    public function getBiberMCount(): int
    {
        return $this->biber_m_count;
    }

    /**
     * @param int $biber_m_count
     */
    public function setBiberMCount(int $biber_m_count): void
    {
        $this->biber_m_count = $biber_m_count;
    }

    /**
     * @return int
     */
    public function getBiberFCount(): int
    {
        return $this->biber_f_count;
    }

    /**
     * @param int $biber_f_count
     */
    public function setBiberFCount(int $biber_f_count): void
    {
        $this->biber_f_count = $biber_f_count;
    }

    /**
     * @return int
     */
    public function getWoelfeMCount(): int
    {
        return $this->woelfe_m_count;
    }

    /**
     * @param int $woelfe_m_count
     */
    public function setWoelfeMCount(int $woelfe_m_count): void
    {
        $this->woelfe_m_count = $woelfe_m_count;
    }

    /**
     * @return int
     */
    public function getWoelfeFCount(): int
    {
        return $this->woelfe_f_count;
    }

    /**
     * @param int $woelfe_f_count
     */
    public function setWoelfeFCount(int $woelfe_f_count): void
    {
        $this->woelfe_f_count = $woelfe_f_count;
    }

    /**
     * @return int
     */
    public function getPfadisMCount(): int
    {
        return $this->pfadis_m_count;
    }

    /**
     * @param int $pfadis_m_count
     */
    public function setPfadisMCount(int $pfadis_m_count): void
    {
        $this->pfadis_m_count = $pfadis_m_count;
    }

    /**
     * @return int
     */
    public function getPfadisFCount(): int
    {
        return $this->pfadis_f_count;
    }

    /**
     * @param int $pfadis_f_count
     */
    public function setPfadisFCount(int $pfadis_f_count): void
    {
        $this->pfadis_f_count = $pfadis_f_count;
    }

    /**
     * @return int
     */
    public function getPiosMCount(): int
    {
        return $this->pios_m_count;
    }

    /**
     * @param int $pios_m_count
     */
    public function setPiosMCount(int $pios_m_count): void
    {
        $this->pios_m_count = $pios_m_count;
    }

    /**
     * @return int
     */
    public function getPiosFCount(): int
    {
        return $this->pios_f_count;
    }

    /**
     * @param int $pios_f_count
     */
    public function setPiosFCount(int $pios_f_count): void
    {
        $this->pios_f_count = $pios_f_count;
    }

    /**
     * @return int
     */
    public function getRoverMCount(): int
    {
        return $this->rover_m_count;
    }

    /**
     * @param int $rover_m_count
     */
    public function setRoverMCount(int $rover_m_count): void
    {
        $this->rover_m_count = $rover_m_count;
    }

    /**
     * @return int
     */
    public function getRoverFCount(): int
    {
        return $this->rover_f_count;
    }

    /**
     * @param int $rover_f_count
     */
    public function setRoverFCount(int $rover_f_count): void
    {
        $this->rover_f_count = $rover_f_count;
    }

    /**
     * @return int
     */
    public function getPtaMCount(): int
    {
        return $this->pta_m_count;
    }

    /**
     * @param int $pta_m_count
     */
    public function setPtaMCount(int $pta_m_count): void
    {
        $this->pta_m_count = $pta_m_count;
    }

    /**
     * @return int
     */
    public function getPtaFCount(): int
    {
        return $this->pta_f_count;
    }

    /**
     * @param int $pta_f_count
     */
    public function setPtaFCount(int $pta_f_count): void
    {
        $this->pta_f_count = $pta_f_count;
    }
}
