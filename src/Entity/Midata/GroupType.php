<?php

namespace App\Entity\Midata;

use App\Repository\Midata\GroupTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_group_type")
 * @ORM\Entity(repositoryClass=GroupTypeRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class GroupType
{
    public const FEDERATION = 'Group::Bund';
    public const CANTON = 'Group::Kantonalverband';
    public const REGION = 'Group::Region';
    public const DEPARTMENT = 'Group::Abteilung';
    public const DEPARTMENT_HIERARCHY = [self::FEDERATION, self::CANTON, self::REGION, self::DEPARTMENT];

    public const BIBER = 'Group::Biber';
    public const WOELFE = 'Group::Woelfe';
    public const PFADI = 'Group::Pfadi';
    public const PIO = 'Group::Pio';
    public const ROVER = 'Group::RegionaleRover';
    public const PTA = 13;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupType;


    /**
     * Returns whether $other is the child department of this
     * @param GroupType $other
     * @return bool
     */
    public function isDepartmentParent(GroupType &$other): bool
    {
        try {
            $index = array_search($this->getGroupType(), self::DEPARTMENT_HIERARCHY);
            if (!$index) {
                return false;
            }

            $childGroupType = self::DEPARTMENT_HIERARCHY[$index + 1];
            $otherGroupType = $other->getGroupType();
            return $childGroupType === $otherGroupType;
        } catch (\Throwable $e) {
            return false;
        }
    }

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
    public function getDeLabel(): ?string
    {
        return $this->deLabel;
    }

    /**
     * @param null|string $label
     */
    public function setDeLabel(?string $label)
    {
        $this->deLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getItLabel(): ?string
    {
        return $this->itLabel;
    }

    /**
     * @param null|string $label
     */
    public function setItLabel(?string $label)
    {
        $this->itLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getFrLabel(): ?string
    {
        return $this->frLabel;
    }

    /**
     * @param null|string $label
     */
    public function setFrLabel(?string $label)
    {
        $this->frLabel = $label;
    }

    /**
     * @return null|string
     */
    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    /**
     * @param null|string $groupType
     */
    public function setGroupType(?string $groupType)
    {
        $this->groupType = $groupType;
    }
}
