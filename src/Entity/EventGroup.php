<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_event_group")
 * @ORM\Entity(repositoryClass="App\Repository\EventGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EventGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
     */
    private $group;

    public function getId()
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event)
    {
        $this->event = $event;
    }

    public function setGroup(?Group $group)
    {
        $this->group = $group;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }
}
