<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910145414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE gamification_person_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE goal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE level_up_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE gamification_person_profile (id INT NOT NULL, person_id INT NOT NULL, level_id INT NOT NULL, has_used_card_layer BOOLEAN NOT NULL, has_used_datafilter BOOLEAN NOT NULL, has_used_timefilter BOOLEAN NOT NULL, has_shared_el BOOLEAN NOT NULL, access_granted_count INT NOT NULL, el_filled_out BOOLEAN NOT NULL, el_revised BOOLEAN NOT NULL, el_irrelevant BOOLEAN NOT NULL, el_improved BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5649C849217BBB47 ON gamification_person_profile (person_id)');
        $this->addSql('CREATE INDEX IDX_5649C8495FB14BA7 ON gamification_person_profile (level_id)');
        $this->addSql('CREATE TABLE goal (id INT NOT NULL, level_id INT NOT NULL, required BOOLEAN NOT NULL, de_title VARCHAR(255) NOT NULL, de_information TEXT NOT NULL, de_help TEXT DEFAULT NULL, fr_title VARCHAR(255) NOT NULL, fr_information TEXT NOT NULL, fr_help TEXT DEFAULT NULL, it_title VARCHAR(255) NOT NULL, it_information TEXT NOT NULL, it_help TEXT DEFAULT NULL, key VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FCDCEB2E5FB14BA7 ON goal (level_id)');
        $this->addSql('CREATE TABLE level (id INT NOT NULL, type INT NOT NULL, de_title VARCHAR(255) NOT NULL, fr_title VARCHAR(255) NOT NULL, it_title VARCHAR(255) NOT NULL, key VARCHAR(255) NOT NULL, next_key VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE level_up_log (id INT NOT NULL, person_id INT NOT NULL, level_id INT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8959129C217BBB47 ON level_up_log (person_id)');
        $this->addSql('CREATE INDEX IDX_8959129C5FB14BA7 ON level_up_log (level_id)');
        $this->addSql('ALTER TABLE gamification_person_profile ADD CONSTRAINT FK_5649C849217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gamification_person_profile ADD CONSTRAINT FK_5649C8495FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2E5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE level_up_log ADD CONSTRAINT FK_8959129C217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE level_up_log ADD CONSTRAINT FK_8959129C5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gamification_person_profile DROP CONSTRAINT FK_5649C8495FB14BA7');
        $this->addSql('ALTER TABLE goal DROP CONSTRAINT FK_FCDCEB2E5FB14BA7');
        $this->addSql('ALTER TABLE level_up_log DROP CONSTRAINT FK_8959129C5FB14BA7');
        $this->addSql('DROP SEQUENCE gamification_person_profile_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE goal_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE level_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE level_up_log_id_seq CASCADE');
        $this->addSql('DROP TABLE gamification_person_profile');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE level_up_log');
    }
}
