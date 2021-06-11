<?php

namespace App\Service;

use Symfony\Component\Console\Output\OutputInterface;

class Profiler
{
    private $output;
    private $name;
    private $started;

    /**
     * Profiler constructor.
     * @param OutputInterface $output
     * @param string $name
     */
    public function __construct(OutputInterface $output, string $name)
    {
        $this->output = $output;
        $this->name = $name;
        $this->started = hrtime(true);
    }

    public function endTimer(): void
    {
        $end = hrtime(true);
        $duration = ($end - $this->started) / 1000000;
        if ($duration > 100) {
            $this->output->writeln(sprintf('<comment>slow %s: %fms</comment>', $this->name, $duration));
        }
    }

    public function restart(): void
    {
        $this->started = hrtime(true);
    }
}
