<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210830082933 extends AbstractMigration
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
        $this->addSql('CREATE SEQUENCE quap_link_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quap_question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quap_questionnaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hc_widget_quap (id INT NOT NULL, questionnaire_id INT NOT NULL, group_id INT DEFAULT NULL, answers JSON NOT NULL, data_point_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC87B8E2CE07E8FF ON hc_widget_quap (questionnaire_id)');
        $this->addSql('CREATE INDEX IDX_EC87B8E2FE54D947 ON hc_widget_quap (group_id)');
        $this->addSql('COMMENT ON COLUMN hc_widget_quap.data_point_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hc_widget_quap.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_aspect (id INT NOT NULL, questionnaire_id INT DEFAULT NULL, local_id INT NOT NULL, name_de VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_it VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, description_de TEXT NOT NULL, description_fr TEXT NOT NULL, description_it TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_37DF8DDCCE07E8FF ON quap_aspect (questionnaire_id)');
        $this->addSql('CREATE UNIQUE INDEX aspect_local_id ON quap_aspect (local_id, questionnaire_id)');
        $this->addSql('COMMENT ON COLUMN quap_aspect.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_aspect.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_help (id INT NOT NULL, question_id INT DEFAULT NULL, help_de TEXT NOT NULL, help_fr TEXT NOT NULL, help_it TEXT NOT NULL, severity INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5002CDF61E27F6BF ON quap_help (question_id)');
        $this->addSql('CREATE UNIQUE INDEX help_local_id ON quap_help (severity, question_id)');
        $this->addSql('COMMENT ON COLUMN quap_help.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_help.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_link (id INT NOT NULL, help_de_id INT DEFAULT NULL, help_fr_id INT DEFAULT NULL, help_it_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6E2908AB58F0FD7A ON quap_link (help_de_id)');
        $this->addSql('CREATE INDEX IDX_6E2908ABEFFEC13C ON quap_link (help_fr_id)');
        $this->addSql('CREATE INDEX IDX_6E2908AB48C50931 ON quap_link (help_it_id)');
        $this->addSql('CREATE TABLE quap_question (id INT NOT NULL, aspect_id INT DEFAULT NULL, local_id INT NOT NULL, question_de TEXT NOT NULL, question_fr TEXT NOT NULL, question_it TEXT NOT NULL, answer_options VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CDD2C18798507F8C ON quap_question (aspect_id)');
        $this->addSql('CREATE UNIQUE INDEX question_local_id ON quap_question (local_id, aspect_id)');
        $this->addSql('COMMENT ON COLUMN quap_question.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quap_question.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quap_questionnaire (id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39037DE78CDE5729 ON quap_questionnaire (type)');
        $this->addSql('ALTER TABLE hc_widget_quap ADD CONSTRAINT FK_EC87B8E2CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES quap_questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hc_widget_quap ADD CONSTRAINT FK_EC87B8E2FE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_aspect ADD CONSTRAINT FK_37DF8DDCCE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES quap_questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_help ADD CONSTRAINT FK_5002CDF61E27F6BF FOREIGN KEY (question_id) REFERENCES quap_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_link ADD CONSTRAINT FK_6E2908AB58F0FD7A FOREIGN KEY (help_de_id) REFERENCES quap_help (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_link ADD CONSTRAINT FK_6E2908ABEFFEC13C FOREIGN KEY (help_fr_id) REFERENCES quap_help (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quap_link ADD CONSTRAINT FK_6E2908AB48C50931 FOREIGN KEY (help_it_id) REFERENCES quap_help (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
        $this->addSql('ALTER TABLE quap_link DROP CONSTRAINT FK_6E2908AB58F0FD7A');
        $this->addSql('ALTER TABLE quap_link DROP CONSTRAINT FK_6E2908ABEFFEC13C');
        $this->addSql('ALTER TABLE quap_link DROP CONSTRAINT FK_6E2908AB48C50931');
        $this->addSql('ALTER TABLE quap_help DROP CONSTRAINT FK_5002CDF61E27F6BF');
        $this->addSql('ALTER TABLE hc_widget_quap DROP CONSTRAINT FK_EC87B8E2CE07E8FF');
        $this->addSql('ALTER TABLE quap_aspect DROP CONSTRAINT FK_37DF8DDCCE07E8FF');
        $this->addSql('DROP SEQUENCE hc_widget_quap_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_aspect_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_help_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_link_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quap_questionnaire_id_seq CASCADE');
        $this->addSql('DROP TABLE hc_widget_quap');
        $this->addSql('DROP TABLE quap_aspect');
        $this->addSql('DROP TABLE quap_help');
        $this->addSql('DROP TABLE quap_link');
        $this->addSql('DROP TABLE quap_question');
        $this->addSql('DROP TABLE quap_questionnaire');
        $this->addSql('DROP INDEX IDX_2742AB8A67D5399D');
        $this->addSql('CREATE INDEX idx_2742ab8a85e16f6b ON admin_geo_address (longitude)');
        $this->addSql('CREATE INDEX idx_2742ab8a4118d123 ON admin_geo_address (latitude)');
    }
}
