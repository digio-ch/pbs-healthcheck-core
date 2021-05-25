<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210422112657 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE admin_geo_address_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hc_widget_geo_location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin_geo_address (id INT NOT NULL, zip INT NOT NULL, town VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, house VARCHAR(255) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2742AB8A421D9546 ON admin_geo_address (zip)');
        $this->addSql('CREATE INDEX IDX_2742AB8A4CE6C7A4 ON admin_geo_address (town)');
        $this->addSql('CREATE INDEX IDX_2742AB8AD4E6F81 ON admin_geo_address (address)');
        $this->addSql('CREATE INDEX IDX_2742AB8A85E16F6B ON admin_geo_address (longitude)');
        $this->addSql('CREATE INDEX IDX_2742AB8A4118D123 ON admin_geo_address (latitude)');
        $this->addSql('CREATE TABLE hc_widget_geo_location (id INT NOT NULL, group_id INT DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, shape VARCHAR(255) NOT NULL, group_type VARCHAR(255) NOT NULL, person_type VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, data_point_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C3255B8FE54D947 ON hc_widget_geo_location (group_id)');
        $this->addSql('CREATE INDEX IDX_6C3255B885E16F6B ON hc_widget_geo_location (longitude)');
        $this->addSql('CREATE INDEX IDX_6C3255B84118D123 ON hc_widget_geo_location (latitude)');
        $this->addSql('CREATE INDEX IDX_6C3255B8EA750E8 ON hc_widget_geo_location (label)');
        $this->addSql('CREATE INDEX IDX_6C3255B8DD30FFD8 ON hc_widget_geo_location (shape)');
        $this->addSql('CREATE INDEX IDX_6C3255B8A5840C59 ON hc_widget_geo_location (group_type)');
        $this->addSql('CREATE INDEX IDX_6C3255B8638D302 ON hc_widget_geo_location (person_type)');
        $this->addSql('CREATE INDEX IDX_6C3255B8EC2BEAC1 ON hc_widget_geo_location (data_point_date)');
        $this->addSql('COMMENT ON COLUMN hc_widget_geo_location.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hc_widget_geo_location.data_point_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE hc_widget_geo_location ADD CONSTRAINT FK_6C3255B8FE54D947 FOREIGN KEY (group_id) REFERENCES midata_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE midata_person ADD geo_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE midata_person ADD CONSTRAINT FK_CCF5FBB88CCA28FB FOREIGN KEY (geo_address_id) REFERENCES admin_geo_address (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CCF5FBB88CCA28FB ON midata_person (geo_address_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE midata_person DROP CONSTRAINT FK_CCF5FBB88CCA28FB');
        $this->addSql('DROP SEQUENCE admin_geo_address_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hc_widget_geo_location_id_seq CASCADE');
        $this->addSql('DROP TABLE admin_geo_address');
        $this->addSql('DROP TABLE hc_widget_geo_location');
        $this->addSql('DROP INDEX IDX_CCF5FBB88CCA28FB');
        $this->addSql('ALTER TABLE midata_person DROP geo_address_id');
    }
}
