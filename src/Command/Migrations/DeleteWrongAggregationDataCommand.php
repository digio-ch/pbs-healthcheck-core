<?php

namespace App\Command\Migrations;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteWrongAggregationDataCommand extends Command
{
    private EntityManagerInterface $em;

    private array $tables = [
        "hc_widget_date",
        "hc_widget_quap",
        "hc_widget_geo_location",
        "hc_widget_leader_overview",
        "hc_widget_demographic_group",
        "hc_widget_demographic_department",
        "hc_widget_demographic_entered_left"];

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->tables as $table){
            $output->writeln(["Deleting from table: $table"]);
            $this->em->getConnection()->executeQuery("
                DELETE FROM $table d
                    WHERE TO_CHAR(d.data_point_date, 'dd') != '01'
                    AND TO_CHAR(d.data_point_date, 'YYYY-MM-dd') != TO_CHAR(CURRENT_TIMESTAMP, 'YYYY-MM-dd');
            ");
        }

        // hc_widget_demographic_camp is referenced by other tables but does not cascade on delete.
        $sql1 = "
        DELETE FROM hc_demographic_camp_group A
            WHERE A.demographic_camp_id IN 
                (SELECT id FROM hc_widget_demographic_camp B 
                    WHERE TO_CHAR(B.data_point_date, 'dd') != '01' 
                        AND TO_CHAR(B.data_point_date, 'YYYY-MM-dd') != TO_CHAR(CURRENT_TIMESTAMP, 'YYYY-MM-dd'));
        ";
        $sql2 = "
        DELETE FROM hc_widget_demographic_camp B
            WHERE TO_CHAR(B.data_point_date, 'dd') != '01' 
                        AND TO_CHAR(B.data_point_date, 'YYYY-MM-dd') != TO_CHAR(CURRENT_TIMESTAMP, 'YYYY-MM-dd');
        ";

        $output->writeln(['Deleting from table: hc_widget_demographic_camp and hc_demographic_camp_group']);
        $this->em->getConnection()->executeQuery($sql1);
        $this->em->getConnection()->executeQuery($sql2);

        $output->writeln(['Successfully deleted all invalid dates.']);
        return 0;
    }
}
