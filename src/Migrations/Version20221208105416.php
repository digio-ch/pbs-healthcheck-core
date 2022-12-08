<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221208105416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'YOU CANNOT UNDO THIS! Converts all questions to manual questions and deletes all computed answers';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            "UPDATE
                    hc_quap_question
                SET
                    answer_options = 'binary',
                    evaluation_function = NULL
                FROM
                    hc_quap_aspect
                    JOIN hc_quap_questionnaire ON hc_quap_aspect.questionnaire_id = hc_quap_questionnaire.id
                WHERE
                    hc_quap_aspect.id = hc_quap_question.aspect_id
                    AND answer_options LIKE '%midata_binary%'
                    AND hc_quap_questionnaire.type = 'Questionnaire::Group::Canton';"
        );

        $this->addSql(
            "UPDATE
                    hc_quap_question
                SET
                    answer_options = 'range',
                    evaluation_function = NULL
                FROM
                    hc_quap_aspect
                    JOIN hc_quap_questionnaire ON hc_quap_aspect.questionnaire_id = hc_quap_questionnaire.id
                WHERE
                    hc_quap_aspect.id = hc_quap_question.aspect_id
                    AND answer_options LIKE '%midata_range%'
	            AND hc_quap_questionnaire.type = 'Questionnaire::Group::Canton';"
        );

        $this->addSql(
            "UPDATE
                    hc_aggregated_quap
                SET
                    computed_answers = '{}'
                FROM
                    hc_quap_questionnaire
                WHERE
                    hc_quap_questionnaire.id = hc_aggregated_quap.questionnaire_id
                    AND hc_quap_questionnaire.type = 'Questionnaire::Group::Canton';"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
