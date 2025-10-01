<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241030131730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE gamification_quap_event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE gamification_quap_event (id INT NOT NULL, person_id INT NOT NULL, group_id INT DEFAULT NULL, questionnaire_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, local_change_index INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA8EDF88217BBB47 ON gamification_quap_event (person_id)');
        $this->addSql('CREATE INDEX IDX_EA8EDF88FE54D947 ON gamification_quap_event (group_id)');
        $this->addSql('CREATE INDEX IDX_EA8EDF88CE07E8FF ON gamification_quap_event (questionnaire_id)');
        $this->addSql('COMMENT ON COLUMN gamification_quap_event.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE gamification_quap_event ADD CONSTRAINT FK_EA8EDF88217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gamification_quap_event ADD CONSTRAINT FK_EA8EDF88FE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gamification_quap_event ADD CONSTRAINT FK_EA8EDF88CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES hc_quap_questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE gamification_quap_event_id_seq CASCADE');
        $this->addSql('DROP TABLE gamification_quap_event');
    }
}
