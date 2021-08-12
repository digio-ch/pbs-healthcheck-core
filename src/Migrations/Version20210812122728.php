<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210812122728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE hc_widget_quap_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quap_aspect_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quap_help_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quap_question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quap_questionnaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hc_widget_quap (id INT NOT NULL, questionnaire_id INT DEFAULT NULL, group_id INT DEFAULT NULL, answers JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, data_point_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC87B8E2CE07E8FF ON hc_widget_quap (questionnaire_id)');
        $this->addSql('CREATE INDEX IDX_EC87B8E2FE54D947 ON hc_widget_quap (group_id)');
        $this->addSql('COMMENT ON COLUMN hc_widget_quap.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hc_widget_quap.data_point_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_aspect (id INT NOT NULL, questionnaire_id INT DEFAULT NULL, name_de VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_it VARCHAR(255) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, local_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_37DF8DDCCE07E8FF ON quap_aspect (questionnaire_id)');
        $this->addSql('CREATE UNIQUE INDEX aspect_local_id ON quap_aspect (local_id, questionnaire_id, deleted_at)');
        $this->addSql('COMMENT ON COLUMN quap_aspect.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_aspect.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_help (id INT NOT NULL, question_id INT DEFAULT NULL, help_de VARCHAR(255) NOT NULL, help_fr VARCHAR(255) NOT NULL, help_it VARCHAR(255) NOT NULL, severity INT NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, local_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5002CDF61E27F6BF ON quap_help (question_id)');
        $this->addSql('CREATE UNIQUE INDEX help_local_id ON quap_help (local_id, question_id, deleted_at)');
        $this->addSql('COMMENT ON COLUMN quap_help.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_help.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_question (id INT NOT NULL, aspect_id INT DEFAULT NULL, question_de VARCHAR(255) NOT NULL, question_fr VARCHAR(255) NOT NULL, question_it VARCHAR(255) NOT NULL, answer_options VARCHAR(255) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, local_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CDD2C18798507F8C ON quap_question (aspect_id)');
        $this->addSql('CREATE UNIQUE INDEX question_local_id ON quap_question (local_id, aspect_id, deleted_at)');
        $this->addSql('COMMENT ON COLUMN quap_question.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_question.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_questionnaire (id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE hc_widget_quap ADD CONSTRAINT FK_EC87B8E2CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES quap_questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hc_widget_quap ADD CONSTRAINT FK_EC87B8E2FE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_aspect ADD CONSTRAINT FK_37DF8DDCCE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES quap_questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_help ADD CONSTRAINT FK_5002CDF61E27F6BF FOREIGN KEY (question_id) REFERENCES quap_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_question ADD CONSTRAINT FK_CDD2C18798507F8C FOREIGN KEY (aspect_id) REFERENCES quap_aspect (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX idx_2742ab8a85e16f6b');
        $this->addSql('DROP INDEX idx_2742ab8a4118d123');
        $this->addSql('CREATE INDEX IDX_2742AB8A67D5399D ON admin_geo_address (house)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quap_question DROP CONSTRAINT FK_CDD2C18798507F8C');
        $this->addSql('ALTER TABLE quap_help DROP CONSTRAINT FK_5002CDF61E27F6BF');
        $this->addSql('ALTER TABLE hc_widget_quap DROP CONSTRAINT FK_EC87B8E2CE07E8FF');
        $this->addSql('ALTER TABLE quap_aspect DROP CONSTRAINT FK_37DF8DDCCE07E8FF');
        $this->addSql('DROP SEQUENCE hc_widget_quap_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_aspect_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_help_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_questionnaire_id_seq CASCADE');
        $this->addSql('DROP TABLE hc_widget_quap');
        $this->addSql('DROP TABLE quap_aspect');
        $this->addSql('DROP TABLE quap_help');
        $this->addSql('DROP TABLE quap_question');
        $this->addSql('DROP TABLE quap_questionnaire');
        $this->addSql('DROP INDEX IDX_2742AB8A67D5399D');
        $this->addSql('CREATE INDEX idx_2742ab8a85e16f6b ON admin_geo_address (longitude)');
        $this->addSql('CREATE INDEX idx_2742ab8a4118d123 ON admin_geo_address (latitude)');
    }
}
