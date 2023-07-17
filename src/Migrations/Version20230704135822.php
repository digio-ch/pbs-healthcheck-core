<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704135822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE group_geo_location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE group_geo_location (id INT NOT NULL, group_id INT DEFAULT NULL, lat VARCHAR(255) NOT NULL, long VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C61B453FE54D947 ON group_geo_location (group_id)');
        $this->addSql('ALTER TABLE group_geo_location ADD CONSTRAINT FK_5C61B453FE54D947 FOREIGN KEY (group_id) REFERENCES statistic_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE group_geo_location_id_seq CASCADE');
        $this->addSql('DROP TABLE group_geo_location');
    }
}
