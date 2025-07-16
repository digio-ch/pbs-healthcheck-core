<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206075749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TYPE hc_status_message_severity AS ENUM (\'none\', \'info\', \'warning\', \'error\')');
        $this->addSql('CREATE TABLE hc_status_message (id INT NOT NULL, severity hc_status_message_severity NOT NULL DEFAULT \'none\', de_message JSON NOT NULL, it_message JSON NOT NULL, fr_message JSON NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE hc_status_message CASCADE');
        $this->addSql('DROP TYPE hc_status_message_severity');
    }
}
