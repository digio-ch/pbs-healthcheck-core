<?php

namespace App\DTO\Model\Apps\Census;

class TableDTO
{
    private int $id;
    private string $name;
    private string $group_type;
    /** @var GroupCensusDTO[] */
    private ?array $data;
    /** @var TableDTO[] */
    private ?array $children;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGroupType(): string
    {
        return $this->group_type;
    }

    /**
     * @param string $group_type
     */
    public function setGroupType(string $group_type): void
    {
        $this->group_type = $group_type;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array|null
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    /**
     * @param array|null $children
     */
    public function setChildren(?array $children): void
    {
        $this->children = $children;
    }
}
