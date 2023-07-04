<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630154534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE statistic_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE statistic_group (id INT NOT NULL, parent_group_id INT NULL, group_type_id INT NOT NULL, canton_id INT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7F70D6D161997596 ON statistic_group (parent_group_id)');
        $this->addSql('CREATE INDEX IDX_7F70D6D1434CD89F ON statistic_group (group_type_id)');
        $this->addSql('CREATE INDEX IDX_7F70D6D18D070D0B ON statistic_group (canton_id)');
        $this->addSql('ALTER TABLE statistic_group ADD CONSTRAINT FK_7F70D6D161997596 FOREIGN KEY (parent_group_id) REFERENCES statistic_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE statistic_group ADD CONSTRAINT FK_7F70D6D1434CD89F FOREIGN KEY (group_type_id) REFERENCES midata_group_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE statistic_group ADD CONSTRAINT FK_7F70D6D18D070D0B FOREIGN KEY (canton_id) REFERENCES statistic_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statistic_group DROP CONSTRAINT FK_7F70D6D161997596');
        $this->addSql('ALTER TABLE statistic_group DROP CONSTRAINT FK_7F70D6D18D070D0B');
        $this->addSql('DROP SEQUENCE statistic_group_id_seq CASCADE');
        $this->addSql('DROP TABLE statistic_group');
    }
}
