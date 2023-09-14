<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230628111910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE group_settings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE group_settings (id INT NOT NULL, group_id INT DEFAULT NULL, role_overview_filter TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC75ECBAFE54D947 ON group_settings (group_id)');
        $this->addSql('COMMENT ON COLUMN group_settings.role_overview_filter IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE group_settings ADD CONSTRAINT FK_EC75ECBAFE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql("INSERT INTO group_settings (id, group_id)
                SELECT
                    nextval('group_settings_id_seq'),
                    midata_group.id
                FROM
                    midata_group;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE group_settings_id_seq CASCADE');
        $this->addSql('DROP TABLE group_settings');
    }
}
