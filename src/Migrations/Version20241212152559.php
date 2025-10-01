<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212152559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login DROP CONSTRAINT FK_AA08CB10217BBB47');
        $this->addSql('ALTER TABLE login ADD CONSTRAINT FK_AA08CB10217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login DROP CONSTRAINT fk_aa08cb10217bbb47');
        $this->addSql('ALTER TABLE login ADD CONSTRAINT fk_aa08cb10217bbb47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
