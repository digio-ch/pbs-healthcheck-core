<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\CommandStatistics;
use Symfony\Component\Console\Command\Command;

abstract class StatisticsCommand extends Command
{
    abstract public function getStats(): CommandStatistics;
}
