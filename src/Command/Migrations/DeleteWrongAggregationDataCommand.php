<?php

namespace App\Command\Migrations;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteWrongAggregationDataCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:migrate:delete-wrong-aggregations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->getConnection()->executeQuery("
            DELETE FROM hc_aggregated_date d
                WHERE TO_CHAR(d.data_point_date, 'dd') != '01'
                AND TO_CHAR(d.data_point_date, 'YYYY-MM-dd') != TO_CHAR(CURRENT_TIMESTAMP, 'YYYY-MM-dd');
        ");

        return 0;
    }
}
