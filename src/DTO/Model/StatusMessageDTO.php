<?php

namespace App\DTO\Model;

class StatusMessageDTO
{
    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $body;

    /**
     * @param string $title
     * @param string $body
     */
    public function __construct(string $title = "", string $body = "")
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
