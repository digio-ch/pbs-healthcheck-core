<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241030153416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE level_up_log ADD displayed BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE level_up_log ALTER date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE level_up_log ALTER date DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN level_up_log.date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE level_up_log DROP displayed');
        $this->addSql('ALTER TABLE level_up_log ALTER date TYPE DATE');
        $this->addSql('ALTER TABLE level_up_log ALTER date DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN level_up_log.date IS NULL');
    }
}
