<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210925185300 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE midata_person ALTER nickname DROP NOT NULL');
        $this->addSql('ALTER TABLE midata_person_event ALTER person_id DROP NOT NULL');
        $this->addSql('ALTER TABLE midata_event_date DROP CONSTRAINT FK_E13DC41771F7E88B');
        $this->addSql('ALTER TABLE midata_event_date ADD CONSTRAINT FK_E13DC41771F7E88B FOREIGN KEY (event_id) REFERENCES midata_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_event DROP CONSTRAINT FK_51E69FDE71F7E88B');
        $this->addSql('ALTER TABLE midata_person_event ADD CONSTRAINT FK_51E69FDE71F7E88B FOREIGN KEY (event_id) REFERENCES midata_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT FK_AFDC017BFE54D947');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT FK_AFDC017BFE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT FK_AFDC017B217BBB47');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT FK_AFDC017B217BBB47 FOREIGN KEY (person_id) REFERENCES midata_person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_group DROP CONSTRAINT FK_2644E31861997596');
        $this->addSql('ALTER TABLE midata_group ADD CONSTRAINT FK_2644E31861997596 FOREIGN KEY (parent_group_id) REFERENCES midata_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE midata_group DROP CONSTRAINT fk_2644e31861997596');
        $this->addSql('ALTER TABLE midata_group ADD CONSTRAINT fk_2644e31861997596 FOREIGN KEY (parent_group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT fk_afdc017bfe54d947');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT fk_afdc017bfe54d947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_role DROP CONSTRAINT fk_afdc017b217bbb47');
        $this->addSql('ALTER TABLE midata_person_role ADD CONSTRAINT fk_afdc017b217bbb47 FOREIGN KEY (person_id) REFERENCES midata_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_event_date DROP CONSTRAINT fk_e13dc41771f7e88b');
        $this->addSql('ALTER TABLE midata_event_date ADD CONSTRAINT fk_e13dc41771f7e88b FOREIGN KEY (event_id) REFERENCES midata_event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_event DROP CONSTRAINT fk_51e69fde71f7e88b');
        $this->addSql('ALTER TABLE midata_person_event ADD CONSTRAINT fk_51e69fde71f7e88b FOREIGN KEY (event_id) REFERENCES midata_event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person_event ALTER person_id SET NOT NULL');
        $this->addSql('ALTER TABLE midata_person ALTER nickname SET NOT NULL');
    }
}
