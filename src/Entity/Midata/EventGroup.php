<?php

namespace App\Entity\Midata;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_event_group')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class EventGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'groups')]
    private $event;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'events')]
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
