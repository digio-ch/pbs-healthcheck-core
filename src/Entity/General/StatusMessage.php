<?php

namespace App\Entity\General;

use App\Repository\General\StatusMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_status_message")
 * @ORM\Entity(repositoryClass=StatusMessageRepository::class)
 */
class StatusMessage
{
    public const NONE = "none";
    public const INFO = "info";
    public const WARNING = "warning";
    public const ERROR = "error";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('none', 'info', 'warning', 'error')")
     */
    private string $severity;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private string $deMessage;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private string $itMessage;

    /**
     * @ORM\Column(type="string", length=500)
     */
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

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
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
}