<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240905091432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE goal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE goal (id INT NOT NULL, level_id INT NOT NULL, required BOOLEAN NOT NULL, de_title VARCHAR(255) NOT NULL, de_information TEXT NOT NULL, de_help TEXT DEFAULT NULL, fr_title VARCHAR(255) NOT NULL, fr_information TEXT NOT NULL, fr_help TEXT DEFAULT NULL, it_title VARCHAR(255) NOT NULL, it_information TEXT NOT NULL, it_help TEXT DEFAULT NULL, key VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FCDCEB2E5FB14BA7 ON goal (level_id)');
        $this->addSql('CREATE TABLE level (id INT NOT NULL, type INT NOT NULL, de_title VARCHAR(255) NOT NULL, fr_title VARCHAR(255) NOT NULL, it_title VARCHAR(255) NOT NULL, key VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2E5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE goal DROP CONSTRAINT FK_FCDCEB2E5FB14BA7');
        $this->addSql('DROP SEQUENCE goal_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE level_id_seq CASCADE');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE level');
    }
}
