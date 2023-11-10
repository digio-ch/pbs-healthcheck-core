<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230720133258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE census_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE census_group (id INT NOT NULL, group_type_id INT NOT NULL, total_count INT NOT NULL, total_m_count INT NOT NULL, total_f_count INT NOT NULL, leiter_m_count INT NOT NULL, leiter_f_count INT NOT NULL, biber_m_count INT NOT NULL, biber_f_count INT NOT NULL, woelfe_m_count INT NOT NULL, woelfe_f_count INT NOT NULL, pfadis_m_count INT NOT NULL, pfadis_f_count INT NOT NULL, pios_m_count INT NOT NULL, pios_f_count INT NOT NULL, rover_m_count INT NOT NULL, rover_f_count INT NOT NULL, pta_m_count INT NOT NULL, pta_f_count INT NOT NULL, name VARCHAR(255) NOT NULL, group_id INT NOT NULL, year VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C90D586434CD89F ON census_group (group_type_id)');
        $this->addSql('ALTER TABLE census_group ADD CONSTRAINT FK_6C90D586434CD89F FOREIGN KEY (group_type_id) REFERENCES midata_group_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE census_group_id_seq CASCADE');
        $this->addSql('DROP TABLE census_group');
    }
}
