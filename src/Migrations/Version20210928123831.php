<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Prepares for adding new required columns midata_id and sync_group_id.
 */
final class Version20210928123831 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // Since we will start using a separate non-unique column for the midata id, we need to
        // empty all the affected tables. This is not a problem, because they can easily be filled
        // again via a sync.
        $this->addSql('DELETE FROM midata_event_date');
        $this->addSql('DELETE FROM midata_person_event');
        $this->addSql('DELETE FROM midata_person_role');
        $this->addSql('DELETE FROM midata_event');
        $this->addSql('DELETE FROM midata_person');
        $this->addSql('DELETE FROM midata_group');
    }

    public function down(Schema $schema) : void
    {
    }
}
