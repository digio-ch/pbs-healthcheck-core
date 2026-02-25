<?php

namespace App\Service\Logger\Messages;

class CommandEndLogMessage extends LogMessage
{
    /** @var string */
    private $args;

    /** @var int $code */
    private $code;

    public function __construct(string $command, array $args, int $code)
    {
        parent::__construct(sprintf('Command \'%s\' exited with code %d', $command, $code), 'command');
        $this->args = $args;
        $this->code = $code;
    }

    /**
     * @return string[]
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
