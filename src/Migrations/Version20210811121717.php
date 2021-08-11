<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811121717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quap_aspect ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quap_aspect ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quap_aspect ADD local_id INT NOT NULL');
        $this->addSql('COMMENT ON COLUMN quap_aspect.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_aspect.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX aspect_local_id ON quap_aspect (local_id, questionnaire_id, deleted_at)');
        $this->addSql('ALTER TABLE quap_help ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quap_help ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quap_help ADD local_id INT NOT NULL');
        $this->addSql('COMMENT ON COLUMN quap_help.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_help.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX help_local_id ON quap_help (local_id, question_id, deleted_at)');
        $this->addSql('ALTER TABLE quap_question ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quap_question ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quap_question ADD local_id INT NOT NULL');
        $this->addSql('COMMENT ON COLUMN quap_question.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_question.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX question_local_id ON quap_question (local_id, aspect_id, deleted_at)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX help_local_id');
        $this->addSql('ALTER TABLE quap_help DROP deleted_at');
        $this->addSql('ALTER TABLE quap_help DROP created_at');
        $this->addSql('ALTER TABLE quap_help DROP local_id');
        $this->addSql('DROP INDEX aspect_local_id');
        $this->addSql('ALTER TABLE quap_aspect DROP deleted_at');
        $this->addSql('ALTER TABLE quap_aspect DROP created_at');
        $this->addSql('ALTER TABLE quap_aspect DROP local_id');
        $this->addSql('DROP INDEX question_local_id');
        $this->addSql('ALTER TABLE quap_question DROP deleted_at');
        $this->addSql('ALTER TABLE quap_question DROP created_at');
        $this->addSql('ALTER TABLE quap_question DROP local_id');
    }
}
