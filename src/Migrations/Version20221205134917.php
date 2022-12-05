<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221205134917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Aggregated Person Table for the Gantt chart';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE aggregated_person_role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE hc_aggregated_person_role (id INT NOT NULL, person_id INT DEFAULT NULL, role_id INT NOT NULL, group_id INT NOT NULL, midata_id INT DEFAULT NULL, nickname VARCHAR(255) NOT NULL, start_at DATE NOT NULL, end_at DATE DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_40B8716BD3728193 ON hc_aggregated_person_role (person_id)');
        $this->addSql('CREATE INDEX IDX_40B8716B88987678 ON hc_aggregated_person_role (role_id)');
        $this->addSql('CREATE INDEX IDX_40B8716B2F68B530 ON hc_aggregated_person_role (group_id)');
        $this->addSql('CREATE INDEX IDX_40B8716B34C0398A ON hc_aggregated_person_role (midata_id)');
        $this->addSql(
            'ALTER TABLE hc_aggregated_person_role ADD CONSTRAINT FK_40B8716BD3728193 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE hc_aggregated_person_role ADD CONSTRAINT FK_40B8716B88987678 FOREIGN KEY (role_id) REFERENCES midata_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE hc_aggregated_person_role ADD CONSTRAINT FK_40B8716B2F68B530 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE hc_aggregated_person_role ADD CONSTRAINT FK_40B8716B34C0398A FOREIGN KEY (midata_id) REFERENCES midata_person_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            "INSERT INTO hc_aggregated_person_role (id, person_id, role_id, group_id, midata_id, nickname, start_at, end_at)
                SELECT
                    nextval('aggregated_person_role_id_seq'),
                    midata_person_role.person_id,
                    midata_person_role.role_id,
                    midata_person_role.group_id,
                    midata_person_role.id,
                    midata_person.nickname,
                    midata_person_role.created_at,
                    midata_person_role.deleted_at
                FROM
                    midata_person_role
                    JOIN midata_person ON midata_person_role.person_id = midata_person.id;"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE aggregated_person_role_id_seq CASCADE');
        $this->addSql('DROP TABLE hc_aggregated_person_role');
    }
}
