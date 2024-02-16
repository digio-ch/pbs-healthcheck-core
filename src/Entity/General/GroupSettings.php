<?php

namespace App\Entity\General;

use App\Entity\Midata\Group;
use App\Entity\Midata\Role;
use App\Repository\General\GroupSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupSettingsRepository::class)
 */
class GroupSettings
{
    public const DEFAULT_DEPARMENT_ROLES = [Role::DEPARTMENT_LEADER, Role::DEPARTMENT_COACH,
        Role::DEPARTMENT_COACH, Role::DEPARTMENT_LEADER_BIBER, Role::DEPARTMENT_LEADER_WOELFE,
        Role::DEPARTMENT_LEADER_PFADI, Role::DEPARTMENT_LEADER_PIO, Role::DEPARTMENT_LEADER_ROVER];
    public const DEFAULT_REGION_ROLES = [Role::REGIONAL_LEADER, Role::REGIONAL_COACH, Role::REGIONAL_FINANCIER,
        Role::REGIONAL_PRESIDENT];
    public const DEFAULT_CANTONAL_ROLES = [Role::CANTONAL_LEADER, Role::CANTONAL_COACH, Role::CANTONAL_FINANCIER,
        Role::CANTONAL_PRESIDENT];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Group::class, inversedBy="groupSettings", cascade={"persist", "remove"})
     */
    private $group;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $roleOverviewFilter = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $census_roles = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $census_groups = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $census_filter_males;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $census_filter_females;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getRoleOverviewFilter(): ?array
    {
        return $this->roleOverviewFilter;
    }

    public function setRoleOverviewFilter(?array $roleOverviewFilter): self
    {
        $this->roleOverviewFilter = $roleOverviewFilter;

        return $this;
    }

    public function getCensusRoles(): ?array
    {
        return $this->census_roles;
    }

    public function setCensusRoles(?array $census_roles): self
    {
        $this->census_roles = $census_roles;

        return $this;
    }

    public function getCensusGroups(): ?array
    {
        return $this->census_groups;
    }

    public function setCensusGroups(?array $census_groups): self
    {
        $this->census_groups = $census_groups;

        return $this;
    }

    public function getCensusFilterMales(): ?bool
    {
        return $this->census_filter_males;
    }

    public function setCensusFilterMales(?bool $census_filter_males): self
    {
        $this->census_filter_males = $census_filter_males;

        return $this;
    }

    public function getCensusFilterFemales(): ?bool
    {
        return $this->census_filter_females;
    }

    public function setCensusFilterFemales(?bool $census_filter_females): self
    {
        $this->census_filter_females = $census_filter_females;

        return $this;
    }
}
