<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909161607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE person_goal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE person_goal (id INT NOT NULL, person_id INT NOT NULL, level_id INT NOT NULL, has_used_card_layer BOOLEAN NOT NULL, has_used_datafilter BOOLEAN NOT NULL, has_used_timefilter BOOLEAN NOT NULL, has_shared_el BOOLEAN NOT NULL, access_granted_count INT NOT NULL, el_filled_out BOOLEAN NOT NULL, el_revised BOOLEAN NOT NULL, el_irrelevant BOOLEAN NOT NULL, el_improved BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_763A6F05217BBB47 ON person_goal (person_id)');
        $this->addSql('CREATE INDEX IDX_763A6F055FB14BA7 ON person_goal (level_id)');
        $this->addSql('ALTER TABLE person_goal ADD CONSTRAINT FK_763A6F05217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE person_goal ADD CONSTRAINT FK_763A6F055FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE person_goal_id_seq CASCADE');
        $this->addSql('DROP TABLE person_goal');
    }
}
