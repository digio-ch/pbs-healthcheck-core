<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250707111216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Adjust foreign key on hc_aggregated_demographic_camp_group referencing hc_aggregated_demographic_camp to cascade
        $this->addSql('ALTER TABLE hc_aggregated_demographic_camp_group DROP CONSTRAINT FK_58535CE26C5AD790');
        $this->addSql('ALTER TABLE hc_aggregated_demographic_camp_group ADD CONSTRAINT FK_58535CE26C5AD790 FOREIGN KEY (demographic_camp_id) REFERENCES hc_aggregated_demographic_camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
