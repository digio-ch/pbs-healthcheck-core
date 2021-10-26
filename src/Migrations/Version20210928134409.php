<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds new midata_id and sync_group_id columns, and several other small changes necessary for the new
 * syncing architecture.
 * In each table, the sync_group_id will hold the id of the Abteilung that was being synced when this entry was
 * created.
 * The midata_id will hold the value that was previously held in the id column. This is necessary because we want
 * to be able to delete one Abteilung's data independently of all other data of other Abteilungen. For this reason,
 * we need to be able to store the same person, event, etc. multiple times (but distinguished by sync_group_id).
 */
final class Version20210928134409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE midata_event ADD sync_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_event ADD midata_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_event ADD CONSTRAINT FK_702AAD7ABF466769 FOREIGN KEY (sync_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_702AAD7ABF466769 ON midata_event (sync_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_702AAD7A2E18B78FBF466769 ON midata_event (midata_id, sync_group_id)');
        $this->addSql('ALTER TABLE midata_event_date DROP CONSTRAINT FK_E13DC41771F7E88B');
        $this->addSql('ALTER TABLE midata_event_date ADD sync_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_event_date ADD midata_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_event_date ADD CONSTRAINT FK_E13DC417BF466769 FOREIGN KEY (sync_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_event_date ADD CONSTRAINT FK_E13DC41771F7E88B FOREIGN KEY (event_id) REFERENCES midata_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E13DC417BF466769 ON midata_event_date (sync_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E13DC4172E18B78FBF466769 ON midata_event_date (midata_id, sync_group_id)');
        $this->addSql('ALTER TABLE midata_group DROP CONSTRAINT FK_2644E31861997596');
        $this->addSql('ALTER TABLE midata_group ADD sync_group_id INT NULL');
        $this->addSql('ALTER TABLE midata_group ADD midata_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_group ADD CONSTRAINT FK_2644E318BF466769 FOREIGN KEY (sync_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_group ADD CONSTRAINT FK_2644E31861997596 FOREIGN KEY (parent_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2644E318BF466769 ON midata_group (sync_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2644E3182E18B78FBF466769 ON midata_group (midata_id, sync_group_id)');
        $this->addSql('ALTER TABLE midata_person ADD sync_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_person ADD midata_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_person ALTER nickname DROP NOT NULL');
        $this->addSql('ALTER TABLE midata_person ADD CONSTRAINT FK_CCF5FBB8BF466769 FOREIGN KEY (sync_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CCF5FBB8BF466769 ON midata_person (sync_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CCF5FBB82E18B78FBF466769 ON midata_person (midata_id, sync_group_id)');
        $this->addSql('ALTER TABLE midata_person_event DROP CONSTRAINT FK_51E69FDE71F7E88B');
        $this->addSql('ALTER TABLE midata_person_event ADD sync_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_person_event ADD midata_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_person_event ALTER person_id DROP NOT NULL');
        $this->addSql('ALTER TABLE midata_person_event ADD CONSTRAINT FK_51E69FDEBF466769 FOREIGN KEY (sync_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_event ADD CONSTRAINT FK_51E69FDE71F7E88B FOREIGN KEY (event_id) REFERENCES midata_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_51E69FDEBF466769 ON midata_person_event (sync_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51E69FDE2E18B78FBF466769 ON midata_person_event (midata_id, sync_group_id)');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT FK_AFDC017BFE54D947');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT FK_AFDC017B217BBB47');
        $this->addSql('ALTER TABLE midata_person_role ADD sync_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_person_role ADD midata_id INT NOT NULL');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT FK_AFDC017BBF466769 FOREIGN KEY (sync_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT FK_AFDC017BFE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT FK_AFDC017B217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AFDC017BBF466769 ON midata_person_role (sync_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AFDC017B2E18B78FBF466769 ON midata_person_role (midata_id, sync_group_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE midata_event_date DROP CONSTRAINT FK_E13DC417BF466769');
        $this->addSql('ALTER TABLE midata_event_date DROP CONSTRAINT fk_e13dc41771f7e88b');
        $this->addSql('DROP INDEX IDX_E13DC417BF466769');
        $this->addSql('DROP INDEX UNIQ_E13DC4172E18B78FBF466769');
        $this->addSql('ALTER TABLE midata_event_date DROP sync_group_id');
        $this->addSql('ALTER TABLE midata_event_date DROP midata_id');
        $this->addSql('ALTER TABLE midata_event_date ADD CONSTRAINT fk_e13dc41771f7e88b FOREIGN KEY (event_id) REFERENCES midata_event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_event DROP CONSTRAINT FK_702AAD7ABF466769');
        $this->addSql('DROP INDEX IDX_702AAD7ABF466769');
        $this->addSql('DROP INDEX UNIQ_702AAD7A2E18B78FBF466769');
        $this->addSql('ALTER TABLE midata_event DROP sync_group_id');
        $this->addSql('ALTER TABLE midata_event DROP midata_id');
        $this->addSql('ALTER TABLE midata_person_event DROP CONSTRAINT FK_51E69FDEBF466769');
        $this->addSql('ALTER TABLE midata_person_event DROP CONSTRAINT fk_51e69fde71f7e88b');
        $this->addSql('DROP INDEX IDX_51E69FDEBF466769');
        $this->addSql('DROP INDEX UNIQ_51E69FDE2E18B78FBF466769');
        $this->addSql('ALTER TABLE midata_person_event DROP sync_group_id');
        $this->addSql('ALTER TABLE midata_person_event DROP midata_id');
        $this->addSql('ALTER TABLE midata_person_event ALTER person_id SET NOT NULL');
        $this->addSql('ALTER TABLE midata_person_event ADD CONSTRAINT fk_51e69fde71f7e88b FOREIGN KEY (event_id) REFERENCES midata_event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT FK_AFDC017BBF466769');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT fk_afdc017bfe54d947');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT fk_afdc017b217bbb47');
        $this->addSql('DROP INDEX IDX_AFDC017BBF466769');
        $this->addSql('DROP INDEX UNIQ_AFDC017B2E18B78FBF466769');
        $this->addSql('ALTER TABLE midata_person_role DROP sync_group_id');
        $this->addSql('ALTER TABLE midata_person_role DROP midata_id');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT fk_afdc017bfe54d947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT fk_afdc017b217bbb47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_group DROP CONSTRAINT FK_2644E318BF466769');
        $this->addSql('ALTER TABLE midata_group DROP CONSTRAINT fk_2644e31861997596');
        $this->addSql('DROP INDEX IDX_2644E318BF466769');
        $this->addSql('DROP INDEX UNIQ_2644E3182E18B78FBF466769');
        $this->addSql('ALTER TABLE midata_group DROP sync_group_id');
        $this->addSql('ALTER TABLE midata_group DROP midata_id');
        $this->addSql('ALTER TABLE midata_group ADD CONSTRAINT fk_2644e31861997596 FOREIGN KEY (parent_group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person DROP CONSTRAINT FK_CCF5FBB8BF466769');
        $this->addSql('DROP INDEX IDX_CCF5FBB8BF466769');
        $this->addSql('DROP INDEX UNIQ_CCF5FBB82E18B78FBF466769');
        $this->addSql('ALTER TABLE midata_person DROP sync_group_id');
        $this->addSql('ALTER TABLE midata_person DROP midata_id');
        $this->addSql('ALTER TABLE midata_person ALTER nickname SET NOT NULL');
    }
}
