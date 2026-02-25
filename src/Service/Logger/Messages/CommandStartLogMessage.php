<?php

namespace App\Service\Logger\Messages;

class CommandStartLogMessage extends LogMessage
{
    /** @var string[] */
    private $args;

    public function __construct(string $command, array $args)
    {
        parent::__construct(sprintf('Command \'%s\' started', $command), 'command');
        $this->args = $args;
    }

    /**
     * @return string[]
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
