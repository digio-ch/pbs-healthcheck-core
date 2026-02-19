<?php

namespace App\Service\DigioLogger\Messages;

abstract class HttpLogMessage extends LogMessage
{
    /** @var string|null $destination */
    private $destination;

    /** @var string */
    private $method;

    /** @var string */
    private $path;

    /** @var string|null */
    private $query;

    public function __construct(string $message, string $type, ?string $destination, string $method, string $path, ?string $query)
    {
        parent::__construct($message, $type);
        $this->destination = $destination;
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
    }

    /**
     * @return string|null
     */
    public function getDestination(): ?string
    {
        return $this->destination;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        $path = $this->path;

        if ($this->destination) {
            $path = $this->destination . $path;
        }

        if ($this->query) {
            $path .= '?' . $this->query;
        }

        return $path;
    }
}
