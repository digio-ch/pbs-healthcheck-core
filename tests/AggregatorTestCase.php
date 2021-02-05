<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class AggregatorTestCase extends WebTestCase
{
    public static function setUpBeforeClass(string $group = null)
    {
        static::$kernel = static::bootKernel(['environment' => 'test']);
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        $constantDataCommand = 'doctrine:fixtures:load --group=constant-data -n';
        $application->run(new StringInput($constantDataCommand));
        $command = 'doctrine:fixtures:load --group ' . $group . ' --append -n';
        $application->run(new StringInput($command));
    }
}
