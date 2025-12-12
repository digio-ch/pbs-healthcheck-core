<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211091728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE hc_security_permission ADD owner_id INT4 NULL, ADD owner_email VARCHAR(255) NULL, ADD pre_expiry_notified bool NULL");
        $this->addSql("ALTER TABLE hc_security_permission ADD CONSTRAINT fk_midata_person FOREIGN KEY (owner_id) REFERENCES midata_person(id)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE hc_security_permission DROP COLUMN owner_id, DROP COLUMN owner_email, DROP COLUMN pre_expiry_notified");
        $this->addSql("ALTER TABLE hc_security_permission DROP CONSTRAINT fk_midata_person");
    }
}
