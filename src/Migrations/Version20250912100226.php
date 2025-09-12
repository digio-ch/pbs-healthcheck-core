<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912100226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SEQUENCE gamification_person_profile_id_seq RENAME TO hc_gamification_person_profile_id_seq");
        $this->addSql("ALTER SEQUENCE gamification_quap_event_id_seq RENAME TO hc_gamification_quap_event_id_seq");
        $this->addSql("ALTER SEQUENCE goal_id_seq RENAME TO hc_gamification_goal_id_seq");
        $this->addSql("ALTER SEQUENCE level_access_id_seq RENAME TO hc_gamification_level_access_id_seq");
        $this->addSql("ALTER SEQUENCE level_id_seq RENAME TO hc_gamification_level_id_seq");
        $this->addSql("ALTER SEQUENCE level_up_log_id_seq RENAME TO hc_gamification_level_up_log_id_seq");
        $this->addSql("ALTER SEQUENCE login_id_seq RENAME TO hc_gamification_login_id_seq");

        $this->addSql("ALTER TABLE gamification_person_profile RENAME TO hc_gamification_person_profile");
        $this->addSql("ALTER TABLE gamification_quap_event RENAME TO hc_gamification_quap_event");
        $this->addSql("ALTER TABLE goal RENAME TO hc_gamification_goal");
        $this->addSql("ALTER TABLE level RENAME TO hc_gamification_level");
        $this->addSql("ALTER TABLE level_access RENAME TO hc_gamification_level_access");
        $this->addSql("ALTER TABLE level_up_log RENAME TO hc_gamification_level_up_log");
        $this->addSql("ALTER TABLE login RENAME TO hc_gamification_login");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SEQUENCE hc_gamification_person_profile_id_seq RENAME TO gamification_person_profile_id_seq");
        $this->addSql("ALTER SEQUENCE hc_gamification_quap_event_id_seq RENAME TO gamification_quap_event_id_seq");
        $this->addSql("ALTER SEQUENCE hc_gamification_goal_id_seq RENAME TO goal_id_seq");
        $this->addSql("ALTER SEQUENCE hc_gamification_level_access_id_seq RENAME TO level_access_id_seq");
        $this->addSql("ALTER SEQUENCE hc_gamification_level_id_seq RENAME TO level_id_seq");
        $this->addSql("ALTER SEQUENCE hc_gamification_level_up_log_id_seq RENAME TO level_up_log_id_seq");
        $this->addSql("ALTER SEQUENCE hc_gamification_login_id_seq RENAME TO login_id_seq");

        $this->addSql("ALTER TABLE hc_gamification_person_profile RENAME TO gamification_person_profile");
        $this->addSql("ALTER TABLE hc_gamification_quap_event RENAME TO gamification_quap_event");
        $this->addSql("ALTER TABLE hc_gamification_goal RENAME TO goal");
        $this->addSql("ALTER TABLE hc_gamification_level RENAME TO level");
        $this->addSql("ALTER TABLE hc_gamification_level_access RENAME TO level_access");
        $this->addSql("ALTER TABLE hc_gamification_level_up_log RENAME TO level_up_log");
        $this->addSql("ALTER TABLE hc_gamification_login RENAME TO login");
    }
}
