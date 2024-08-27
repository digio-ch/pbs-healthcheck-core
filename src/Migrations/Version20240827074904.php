<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240827074904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE login_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE login (id INT NOT NULL, person_id INT DEFAULT NULL, group_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_group_change BOOLEAN NOT NULL, role VARCHAR(255) NOT NULL, hashed_person_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AA08CB10217BBB47 ON login (person_id)');
        $this->addSql('CREATE INDEX IDX_AA08CB10FE54D947 ON login (group_id)');
        $this->addSql('ALTER TABLE login ADD CONSTRAINT FK_AA08CB10217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE login ADD CONSTRAINT FK_AA08CB10FE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE login_id_seq CASCADE');
        $this->addSql('DROP TABLE login');
    }
}
