<?php

namespace App\Entity\General;

use App\Entity\Types\LegacyArrayType;
use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\Group;
use App\Entity\Midata\Role;
use App\Repository\General\PersonSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_group_settings')]
#[ORM\Entity(repositoryClass: PersonSettingsRepository::class)]
class GroupSettings
{
    public const DEFAULT_DEPARMENT_ROLES = [Role::DEPARTMENT_LEADER, Role::DEPARTMENT_COACH,
        Role::DEPARTMENT_COACH, Role::DEPARTMENT_LEADER_BIBER, Role::DEPARTMENT_LEADER_WOELFE,
        Role::DEPARTMENT_LEADER_PFADI, Role::DEPARTMENT_LEADER_PIO, Role::DEPARTMENT_LEADER_ROVER];
    public const DEFAULT_REGION_ROLES = [Role::REGIONAL_LEADER, Role::REGIONAL_COACH, Role::REGIONAL_FINANCIER,
        Role::REGIONAL_PRESIDENT];
    public const DEFAULT_CANTONAL_ROLES = [Role::CANTONAL_LEADER, Role::CANTONAL_COACH, Role::CANTONAL_FINANCIER,
        Role::CANTONAL_PRESIDENT];
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Group::class, inversedBy: 'groupSettings', cascade: ['persist', 'remove'])]
    private ?Group $group = null;

    #[ORM\Column(type: LegacyArrayType::NAME, nullable: true)]
    private $roleOverviewFilter = [];

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
}
