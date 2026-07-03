<?php

namespace App\Entity\Overview;

use Doctrine\DBAL\Types\Types;
use DateTimeImmutable;
use App\Repository\Overview\OverviewSharedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_overview_shared')]
#[ORM\Entity(repositoryClass: OverviewSharedRepository::class)]
class OverviewShared
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'group_id', type: Types::INTEGER)]
    private int $groupId;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
