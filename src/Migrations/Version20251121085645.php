<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121085645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // hc_gamification_quap_event table should store the aspect_local_id
        $this->addSql('ALTER TABLE hc_gamification_quap_event RENAME COLUMN local_change_index TO aspect_local_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hc_gamification_quap_event RENAME COLUMN aspect_local_id TO local_change_index');
    }
}
