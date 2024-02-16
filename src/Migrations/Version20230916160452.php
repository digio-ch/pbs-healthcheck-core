<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230916160452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_settings ADD census_roles TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_settings ADD census_groups TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_settings ADD census_filter_males BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE group_settings ADD census_filter_females BOOLEAN DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN group_settings.census_roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN group_settings.census_groups IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE group_settings DROP census_roles');
        $this->addSql('ALTER TABLE group_settings DROP census_groups');
        $this->addSql('ALTER TABLE group_settings DROP census_filter_males');
        $this->addSql('ALTER TABLE group_settings DROP census_filter_females');
    }
}
