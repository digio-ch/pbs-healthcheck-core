<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_widget_demographic_entered_left", indexes={
 *     @ORM\Index(columns={"new_count_m"}),
 *     @ORM\Index(columns={"new_count_leader_m"}),
 *     @ORM\Index(columns={"exit_count_m"}),
 *     @ORM\Index(columns={"exit_count_leader_m"}),
 *     @ORM\Index(columns={"new_count_f"}),
 *     @ORM\Index(columns={"new_count_leader_f"}),
 *     @ORM\Index(columns={"exit_count_leader_f"}),
 *     @ORM\Index(columns={"exit_count_f"}),
 *     @ORM\Index(columns={"new_count_u"}),
 *     @ORM\Index(columns={"new_count_leader_u"}),
 *     @ORM\Index(columns={"exit_count_u"}),
 *     @ORM\Index(columns={"exit_count_leader_u"}),
 *     @ORM\Index(columns={"group_type"}),
 *     @ORM\Index(columns={"data_point_date"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\WidgetDemographicEnteredLeftRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WidgetDemographicEnteredLeft extends Widget
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newCountM = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newCountLeaderM = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $exitCountM = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $exitCountLeaderM = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newCountF = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newCountLeaderF = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $exitCountLeaderF = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $exitCountF = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newCountU = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newCountLeaderU = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $exitCountU = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $exitCountLeaderU = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupType;

    /**
     * @param int $newCountM
     */
    public function setNewCountM(int $newCountM): void
    {
        $this->newCountM = $newCountM;
    }

    /**
     * @return mixed
     */
    public function getNewCountM()
    {
        return $this->newCountM;
    }

    /**
     * @return mixed
     */
    public function getNewCountLeaderM()
    {
        return $this->newCountLeaderM;
    }

    /**
     * @param int $newCountLeaderM
     */
    public function setNewCountLeaderM(int $newCountLeaderM): void
    {
        $this->newCountLeaderM = $newCountLeaderM;
    }

    /**
     * @return mixed
     */
    public function getExitCountM()
    {
        return $this->exitCountM;
    }

    /**
     * @param int $exitCountM
     */
    public function setExitCountM(int $exitCountM): void
    {
        $this->exitCountM = $exitCountM;
    }

    /**
     * @param int $exitCountLeaderM
     */
    public function setExitCountLeaderM(int $exitCountLeaderM): void
    {
        $this->exitCountLeaderM = $exitCountLeaderM;
    }

    /**
     * @return mixed
     */
    public function getExitCountLeaderM()
    {
        return $this->exitCountLeaderM;
    }

    /**
     * @param int $newCountF
     */
    public function setNewCountF(int $newCountF)
    {
        $this->newCountF = $newCountF;
    }

    /**
     * @return mixed
     */
    public function getNewCountF()
    {
        return $this->newCountF;
    }

    /**
     * @param int $newCountLeaderF
     */
    public function setNewCountLeaderF(int $newCountLeaderF)
    {
        $this->newCountLeaderF = $newCountLeaderF;
    }

    /**
     * @return mixed
     */
    public function getNewCountLeaderF()
    {
        return $this->newCountLeaderF;
    }

    /**
     * @param int $exitCountLeaderF
     */
    public function setExitCountLeaderF(int $exitCountLeaderF)
    {
        $this->exitCountLeaderF = $exitCountLeaderF;
    }

    /**
     * @return mixed
     */
    public function getExitCountLeaderF()
    {
        return $this->exitCountLeaderF;
    }

    /**
     * @param int $exitCountF
     */
    public function setExitCountF(int $exitCountF)
    {
        $this->exitCountF = $exitCountF;
    }

    /**
     * @return mixed
     */
    public function getExitCountF()
    {
        return $this->exitCountF;
    }

    /**
     * @param int $newCountU
     */
    public function setNewCountU(int $newCountU)
    {
        $this->newCountU = $newCountU;
    }

    /**
     * @return mixed
     */
    public function getNewCountU()
    {
        return $this->newCountU;
    }

    /**
     * @param int $newCountLeaderU
     */
    public function setNewCountLeaderU(int $newCountLeaderU)
    {
        $this->newCountLeaderU = $newCountLeaderU;
    }

    /**
     * @return mixed
     */
    public function getNewCountLeaderU()
    {
        return $this->newCountLeaderU;
    }

    /**
     * @param int $exitCountU
     */
    public function setExitCountU(int $exitCountU)
    {
        $this->exitCountU = $exitCountU;
    }

    /**
     * @return mixed
     */
    public function getExitCountU()
    {
        return $this->exitCountU;
    }

    /**
     * @param int $exitCountLeaderU
     */
    public function setExitCountLeaderU(int $exitCountLeaderU)
    {
        $this->exitCountLeaderU = $exitCountLeaderU;
    }

    /**
     * @return mixed
     */
    public function getExitCountLeaderU()
    {
        return $this->exitCountLeaderU;
    }

    /**
     * @param string $groupType
     */
    public function setGroupType(string $groupType)
    {
        $this->groupType = $groupType;
    }

    /**
     * @return mixed
     */
    public function getGroupType()
    {
        return $this->groupType;
    }
}
