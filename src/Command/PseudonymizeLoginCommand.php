<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Repository\Admin\GeoAddressRepository;
use App\Repository\Gamification\LoginRepository;
use App\Repository\Midata\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PseudonymizeLoginCommand extends StatisticsCommand
{
    private LoginRepository $loginRepository;

    private float $duration;

    public function __construct(
        LoginRepository $loginRepository
    ) {
        parent::__construct();
        $this->loginRepository = $loginRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:pseudonomize-login")
            ->addOption("log", '', InputArgument::OPTIONAL, "List all pseudonymized Logins.", false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $pseudonymizedLogins = $this->loginRepository->pseudonymizeAllOlderThan18Months(function ($personId) {
            return hash('sha256', $personId);
        }); // sha256 is mostly collision free and currently irreversible, sufficient for our purposes.
        $log = $input->getOption('log');
        if ($log) {
            $output->writeln('Following Logins (id) have been pseudonymized:');
            foreach ($pseudonymizedLogins as $login) {
                $output->writeln('id: ' . $login->getId());
            }
            $output->writeln('Total of ' . sizeof($pseudonymizedLogins) . ' logins have been pseudonymized.');
        }
        $this->duration = microtime(true) - $start;
        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->duration, '');
    }
}
