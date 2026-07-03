<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\PersonEventTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person_event_type')]
#[ORM\Entity(repositoryClass: PersonEventTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PersonEventType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $deLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $itLabel = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $frLabel = null;

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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType(?string $type)
    {
        $this->type = $type;
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
}
