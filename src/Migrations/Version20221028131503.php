<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028131503 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE hc_widget_demographic_camp RENAME TO hc_aggregated_demographic_camp;');
        $this->addSql('ALTER TABLE hc_demographic_camp_group RENAME TO hc_aggregated_demographic_camp_group;');
        $this->addSql('ALTER TABLE hc_widget_demographic_department RENAME TO hc_aggregated_demographic_department;');
        $this->addSql('ALTER TABLE hc_widget_demographic_entered_left RENAME TO hc_aggregated_demographic_entered_left;');
        $this->addSql('ALTER TABLE hc_leader_overview_leader RENAME TO hc_aggregated_leader_overview_leader;');
        $this->addSql('ALTER TABLE hc_leader_overview_qualification RENAME TO hc_aggregated_leader_overview_qualification;');
        $this->addSql('ALTER TABLE hc_widget_leader_overview RENAME TO hc_aggregated_leader_overview;');
        $this->addSql('ALTER TABLE hc_widget_geo_location RENAME TO hc_aggregated_geo_location;');
        $this->addSql('ALTER TABLE quap_questionnaire RENAME TO hc_quap_questionnaire;');
        $this->addSql('ALTER TABLE quap_question RENAME TO hc_quap_question;');
        $this->addSql('ALTER TABLE quap_help RENAME TO hc_quap_help;');
        $this->addSql('ALTER TABLE quap_link RENAME TO hc_quap_link;');
        $this->addSql('ALTER TABLE quap_aspect RENAME TO hc_quap_aspect;');
        $this->addSql('ALTER TABLE hc_widget_date RENAME TO hc_aggregated_date;');
        $this->addSql('ALTER TABLE hc_widget_quap RENAME TO hc_aggregated_quap;');
        $this->addSql('ALTER TABLE hc_permission_type RENAME TO hc_security_permission_type;');
        $this->addSql('ALTER TABLE hc_permission RENAME TO hc_security_permission;');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE hc_aggregated_demographic_camp RENAME TO hc_widget_demographic_camp;');
        $this->addSql('ALTER TABLE hc_aggregated_demographic_camp_group RENAME TO hc_demographic_camp_group;');
        $this->addSql('ALTER TABLE hc_aggregated_demographic_department RENAME TO hc_widget_demographic_department;');
        $this->addSql('ALTER TABLE hc_aggregated_demographic_entered_left RENAME TO hc_widget_demographic_entered_left;');
        $this->addSql('ALTER TABLE hc_aggregated_leader_overview_leader RENAME TO hc_leader_overview_leader;');
        $this->addSql('ALTER TABLE hc_aggregated_leader_overview_qualification RENAME TO hc_leader_overview_qualification;');
        $this->addSql('ALTER TABLE hc_aggregated_leader_overview RENAME TO hc_widget_leader_overview;');
        $this->addSql('ALTER TABLE hc_aggregated_geo_location RENAME TO hc_widget_geo_location;');
        $this->addSql('ALTER TABLE hc_quap_questionnaire RENAME TO quap_questionnaire;');
        $this->addSql('ALTER TABLE hc_quap_question RENAME TO quap_question;');
        $this->addSql('ALTER TABLE hc_quap_help RENAME TO quap_help;');
        $this->addSql('ALTER TABLE hc_quap_link RENAME TO quap_link;');
        $this->addSql('ALTER TABLE hc_quap_aspect RENAME TO quap_aspect;');
        $this->addSql('ALTER TABLE hc_aggregated_date RENAME TO hc_widget_date;');
        $this->addSql('ALTER TABLE hc_aggregated_quap RENAME TO hc_widget_quap;');
        $this->addSql('ALTER TABLE hc_security_permission_type RENAME TO hc_permission_type;');
        $this->addSql('ALTER TABLE hc_security_permission RENAME TO hc_permission;');
    }
}
