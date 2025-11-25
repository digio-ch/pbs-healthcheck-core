<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119144255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // make group_id unique
        $this->addSql('ALTER TABLE hc_overview_shared ADD CONSTRAINT group_id_unique UNIQUE (group_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE hc_overview_shared DROP CONSTRAINT group_id_unique');
    }
}
