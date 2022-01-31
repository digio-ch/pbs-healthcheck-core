<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220131124047 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE hc_invite_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE hc_permission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hc_permission_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hc_permission (id INT NOT NULL, person_id INT DEFAULT NULL, permission_type_id INT DEFAULT NULL, group_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECCD2277217BBB47 ON hc_permission (person_id)');
        $this->addSql('CREATE INDEX IDX_ECCD2277F25D6DC4 ON hc_permission (permission_type_id)');
        $this->addSql('CREATE INDEX IDX_ECCD2277FE54D947 ON hc_permission (group_id)');
        $this->addSql('CREATE INDEX IDX_ECCD2277E7927C74 ON hc_permission (email)');
        $this->addSql('CREATE TABLE hc_permission_type (id INT NOT NULL, key VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_it VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB8EC5AB8A90ABA9 ON hc_permission_type (key)');
        $this->addSql('ALTER TABLE hc_permission ADD CONSTRAINT FK_ECCD2277217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hc_permission ADD CONSTRAINT FK_ECCD2277F25D6DC4 FOREIGN KEY (permission_type_id) REFERENCES hc_permission_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hc_permission ADD CONSTRAINT FK_ECCD2277FE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql("
            INSERT INTO hc_permission_type (id, key, name_de, name_fr, name_it)
                VALUES
                    (1, 'owner', 'Besitzer', 'Propriétaire', 'Proprietario'),
                    (2, 'editor', 'Bearbeiter', 'Éditeur', 'Editore'),
                    (3, 'viewer', 'Betrachter', 'Spectateur', 'Spettatore')
        ");

        $this->addSql("
            INSERT INTO hc_permission (id, person_id, permission_type_id, group_id, email, expiration_date)
                SELECT nextval('hc_permission_id_seq'), NULL, 3, group_id, email, expiration_date FROM hc_invite 
        ");

        $this->addSql('DROP TABLE hc_invite');
        $this->addSql('ALTER TABLE hc_widget_quap ALTER computed_answers DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hc_permission DROP CONSTRAINT FK_ECCD2277F25D6DC4');
        $this->addSql('DROP SEQUENCE hc_permission_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hc_permission_type_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE hc_invite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hc_invite (id INT NOT NULL, group_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fc6bb860fe54d947 ON hc_invite (group_id)');
        $this->addSql('ALTER TABLE hc_invite ADD CONSTRAINT fk_fc6bb860fe54d947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE hc_permission');
        $this->addSql('DROP TABLE hc_permission_type');
        $this->addSql('ALTER TABLE hc_widget_quap ALTER computed_answers SET DEFAULT \'[]\'');
    }
}
