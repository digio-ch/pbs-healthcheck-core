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
    public static string $NONE = "none";
    public static string $INFO = "info";
    public static string $WARNING = "warning";
    public static string $ERROR = "error";

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