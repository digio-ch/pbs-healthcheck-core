<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_event_date", indexes={
 *     @ORM\Index(columns={"start_at"}),
 *     @ORM\Index(columns={"end_at"})
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"midata_id", "sync_group_id"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\EventDateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EventDate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $midataId;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="sync_group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $syncGroup;

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $endAt;

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
     * @return mixed
     */
    public function getMidataId()
    {
        return $this->midataId;
    }

    /**
     * @param int $midataId
     */
    public function setMidataId(int $midataId)
    {
        $this->midataId = $midataId;
    }

    public function getSyncGroup(): Group
    {
        return $this->syncGroup;
    }

    public function setSyncGroup($syncGroup)
    {
        $this->syncGroup = $syncGroup;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    /**
     * @param DateTimeImmutable|null $startAt
     */
    public function setStartAt(?DateTimeImmutable $startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    /**
     * @param DateTimeImmutable|null $endAt
     */
    public function setEndAt(?DateTimeImmutable $endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @param Event|null $event
     */
    public function setEvent(?Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
