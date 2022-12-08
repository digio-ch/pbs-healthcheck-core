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
                    evaluation_function = null
                WHERE
                    answer_options LIKE '%midata%';"
        );

        $this->addSql(
            "UPDATE
                    hc_aggregated_quap
                SET
                    computed_answers = '{}';"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
