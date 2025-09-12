<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912073257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE level_access_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE level_access (id INT NOT NULL, de_description VARCHAR(255) NOT NULL, fr_description VARCHAR(255) NOT NULL, it_description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE level ADD COLUMN access_id INT NULL DEFAULT NULL, ADD CONSTRAINT fK_level_access FOREIGN KEY(access_id) REFERENCES level_access(id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE level DROP CONSTRAINT fK_level_access, DROP COLUMN access_id');
        $this->addSql('DROP TABLE level_access');
        $this->addSql('DROP SEQUENCE level_access_id_seq');
    }
}
