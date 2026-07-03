<?php

namespace App\Entity\General;

use Doctrine\DBAL\Types\Types;
use App\Repository\General\StatusMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_status_message')]
#[ORM\Entity(repositoryClass: StatusMessageRepository::class)]
class StatusMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: 'hc_status_message_severity')]
    private StatusMessageSeverity $severity;

    #[ORM\Column(type: Types::STRING, length: 500)]
    private string $deMessage;

    #[ORM\Column(type: Types::STRING, length: 500)]
    private string $itMessage;

    #[ORM\Column(type: Types::STRING, length: 500)]
    private string $frMessage;


    /**
     * @param string $lang
     * @return string
     */
    public function getMessage(string $lang): string
    {
        switch ($lang) {
            case "it":
                return $this->getItMessage();
            case "fr":
                return $this->getFrMessage();
            case "de":
            default:
                return $this->getDeMessage();
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getDeMessage(): string
    {
        return $this->deMessage;
    }

    public function setDeMessage(string $deMessage): void
    {
        $this->deMessage = $deMessage;
    }

    public function getItMessage(): string
    {
        return $this->itMessage;
    }

    public function setItMessage(string $itMessage): void
    {
        $this->itMessage = $itMessage;
    }

    public function getFrMessage(): string
    {
        return $this->frMessage;
    }

    public function setFrMessage(string $frMessage): void
    {
        $this->frMessage = $frMessage;
    }

    public function getSeverity(): StatusMessageSeverity
    {
        return $this->severity;
    }

    public function setSeverity(StatusMessageSeverity $severity): void
    {
        $this->severity = $severity;
    }
}
