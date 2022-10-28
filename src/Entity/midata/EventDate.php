<?php

namespace App\Entity\midata;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_event_date", indexes={
 *     @ORM\Index(columns={"start_at"}),
 *     @ORM\Index(columns={"end_at"})
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
     * @ORM\ManyToOne(targetEntity=Event::class)
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
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
