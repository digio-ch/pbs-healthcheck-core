<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909162109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE gamification_person_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE gamification_person_profile (id INT NOT NULL, person_id INT NOT NULL, level_id INT NOT NULL, has_used_card_layer BOOLEAN NOT NULL, has_used_datafilter BOOLEAN NOT NULL, has_used_timefilter BOOLEAN NOT NULL, has_shared_el BOOLEAN NOT NULL, access_granted_count INT NOT NULL, el_filled_out BOOLEAN NOT NULL, el_revised BOOLEAN NOT NULL, el_irrelevant BOOLEAN NOT NULL, el_improved BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5649C849217BBB47 ON gamification_person_profile (person_id)');
        $this->addSql('CREATE INDEX IDX_5649C8495FB14BA7 ON gamification_person_profile (level_id)');
        $this->addSql('ALTER TABLE gamification_person_profile ADD CONSTRAINT FK_5649C849217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gamification_person_profile ADD CONSTRAINT FK_5649C8495FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE gamification_person_profile_id_seq CASCADE');
        $this->addSql('DROP TABLE gamification_person_profile');
    }
}
