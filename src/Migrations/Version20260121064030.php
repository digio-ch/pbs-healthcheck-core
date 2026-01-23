<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121064030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER SEQUENCE group_settings_id_seq RENAME TO hc_group_settings_id_seq');
        $this->addSql('ALTER TABLE group_settings RENAME TO hc_group_settings');
        $this->addSql('ALTER TABLE hc_group_settings DROP COLUMN census_roles');
        $this->addSql('ALTER TABLE hc_group_settings DROP COLUMN census_groups');
        $this->addSql('ALTER TABLE hc_group_settings DROP COLUMN census_filter_males');
        $this->addSql('ALTER TABLE hc_group_settings DROP COLUMN census_filter_females');

        $this->addSql('CREATE SEQUENCE hc_person_settings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hc_person_settings (
                id INT NOT NULL, 
                group_id INT NOT NULL,
                person_id INT NOT NULL,
                census_filter_roles TEXT DEFAULT NULL,
                census_filter_groups TEXT DEFAULT NULL,
                census_filter_males BOOLEAN DEFAULT NULL,
                census_filter_females BOOLEAN DEFAULT NULL,
                PRIMARY KEY(id),
                FOREIGN KEY (group_id) REFERENCES midata_group (id) ON DELETE CASCADE,
                FOREIGN KEY (person_id) REFERENCES midata_person (id) ON DELETE CASCADE,
                UNIQUE(group_id, person_id)
            )'
        );
        $this->addSql('COMMENT ON COLUMN hc_person_settings.census_filter_roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN hc_person_settings.census_filter_groups IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER SEQUENCE hc_group_settings_id_seq RENAME TO group_settings_id_seq');
        $this->addSql('ALTER TABLE hc_group_settings RENAME TO group_settings');
        $this->addSql('ALTER TABLE group_settings ADD census_roles TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_settings ADD census_groups TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_settings ADD census_filter_males BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE group_settings ADD census_filter_females BOOLEAN DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN group_settings.census_roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN group_settings.census_groups IS \'(DC2Type:array)\'');

        $this->addSql('DROP SEQUENCE hc_person_settings_id_seq CASCADE');
        $this->addSql('DROP TABLE hc_person_settings');
    }
}
