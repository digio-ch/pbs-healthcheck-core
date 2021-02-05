<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201123152829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Empties all data in aggregated tables';
    }

    public function up(Schema $schema): void
    {
        // truncate relevant aggregated tables
        $this->addSql('TRUNCATE TABLE hc_widget_demographic_camp RESTART IDENTITY CASCADE');
        $this->addSql('TRUNCATE TABLE hc_widget_demographic_department RESTART IDENTITY CASCADE');
        $this->addSql('TRUNCATE TABLE hc_widget_demographic_entered_left RESTART IDENTITY CASCADE');
        $this->addSql('TRUNCATE TABLE hc_widget_demographic_group RESTART IDENTITY CASCADE');
        $this->addSql('TRUNCATE TABLE hc_widget_leader_overview RESTART IDENTITY CASCADE');

        // restart at 1
        $this->addSql('ALTER SEQUENCE hc_widget_demographic_camp_id_seq RESTART WITH 1');
        $this->addSql('ALTER SEQUENCE hc_widget_demographic_department_id_seq RESTART WITH 1');
        $this->addSql('ALTER SEQUENCE hc_widget_demographic_entered_left_id_seq RESTART WITH 1');
        $this->addSql('ALTER SEQUENCE hc_widget_demographic_group_id_seq RESTART WITH 1');
        $this->addSql('ALTER SEQUENCE hc_widget_leader_overview_id_seq RESTART WITH 1');
        $this->addSql('ALTER SEQUENCE hc_leader_overview_leader_id_seq RESTART WITH 1');
        $this->addSql('ALTER SEQUENCE hc_leader_overview_qualification_id_seq RESTART WITH 1');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('This migration cannot be reversed');
    }
}
