<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124144442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Change level.access_id ON DELETE to SET NULL
        $this->addSql('ALTER TABLE hc_gamification_level DROP CONSTRAINT fK_level_access');
        $this->addSql('ALTER TABLE hc_gamification_level ADD CONSTRAINT fK_level_access FOREIGN KEY(access_id) REFERENCES hc_gamification_level_access(id) ON DELETE SET NULL');

        // Add column key to the hc_gamification_level_access table
        $this->addSql('ALTER TABLE hc_gamification_level_access ADD COLUMN "key" INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE hc_gamification_level_access ALTER COLUMN "key" DROP DEFAULT');

        // Make key columns unique for the gamification translation tables
        $this->addSql('ALTER TABLE hc_gamification_level_access ADD CONSTRAINT level_access_key_unique UNIQUE ("key")');
        $this->addSql('ALTER TABLE hc_gamification_level ADD CONSTRAINT level_key_unique UNIQUE ("key")');
        $this->addSql('ALTER TABLE hc_gamification_goal ADD CONSTRAINT goal_key_unique UNIQUE ("key")');
    }

    public function down(Schema $schema): void
    {
        // Remove unique constraints
        $this->addSql('ALTER TABLE hc_gamification_level_access DROP CONSTRAINT level_access_key_unique');
        $this->addSql('ALTER TABLE hc_gamification_level DROP CONSTRAINT level_key_unique');
        $this->addSql('ALTER TABLE hc_gamification_goal DROP CONSTRAINT goal_key_unique');

        // Drop the key column
        $this->addSql('ALTER TABLE hc_gamification_level_access DROP COLUMN "key"');

        // Restore the original foreign key on hc_gamification_level
        $this->addSql('ALTER TABLE hc_gamification_level DROP CONSTRAINT fK_level_access');
        $this->addSql('ALTER TABLE hc_gamification_level ADD CONSTRAINT fK_level_access FOREIGN KEY (access_id) REFERENCES hc_gamification_level_access (id) ON DELETE CASCADE');
    }
}
