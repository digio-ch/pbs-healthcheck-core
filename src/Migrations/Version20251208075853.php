<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208075853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO hc_security_permission_type (id, key, name_de, name_fr, name_it) VALUES (4, 'editor-plus', 'Bearbeiter Plus', 'Éditeur Plus', 'Editore Plus')");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM hc_security_permission_type WHERE id = 4");
    }
}
