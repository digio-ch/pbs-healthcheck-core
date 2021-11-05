<?php

namespace App\Command;

use App\Entity\Aspect;
use App\Entity\Help;
use App\Entity\Link;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Model\CommandStatistics;
use App\Repository\AspectRepository;
use App\Repository\HelpRepository;
use App\Repository\QuestionnaireRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonMachine\JsonMachine;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportQuestionnairesCommand extends StatisticsCommand
{

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var QuestionnaireRepository $questionnaireRepo
     */
    private $questionnaireRepo;

    /**
     * @var AspectRepository $aspectRepo
     */
    private $aspectRepo;

    /**
     * @var QuestionRepository $questionRepo
     */
    private $questionRepo;

    /**
     * @var HelpRepository $helpRepo
     */
    private $helpRepo;

    /**
     * @var string $pathToJson
     */
    private $pathToJson = "imports/questionnaire_imports.json";


    /**
     * @param EntityManagerInterface $em
     * @param QuestionnaireRepository $questionnaireRepo
     * @param AspectRepository $aspectRepo
     * @param QuestionRepository $questionRepo
     * @param HelpRepository $helpRepo
     */
    public function __construct(
        EntityManagerInterface $em,
        QuestionnaireRepository $questionnaireRepo,
        AspectRepository $aspectRepo,
        QuestionRepository $questionRepo,
        HelpRepository $helpRepo
    ) {
        parent::__construct();

        $this->em = $em;
        $this->questionnaireRepo = $questionnaireRepo;
        $this->aspectRepo = $aspectRepo;
        $this->questionRepo = $questionRepo;
        $this->helpRepo = $helpRepo;
    }

    protected function configure()
    {
        $this
            ->setName("app:import-questionnaire");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Starting import of questionnaires...");

        if (!file_exists($this->pathToJson)) {
            $output->writeln("No data to import. File at " . $this->pathToJson . " not found.");
            return 1;
        }

        // Remove all links
        $this->em->getConnection()->executeQuery('DELETE FROM quap_link');

        $questionnaires = JsonMachine::fromFile($this->pathToJson);
        foreach ($questionnaires as $questionnaire) {
            $this->importQuestionnaire($questionnaire);
        }

        $output->writeln("Questionnaire import process has finished.");
        return 0;
    }

    /**
     * @param $questionnaire
     */
    private function importQuestionnaire($questionnaire): void
    {
        $db_questionnaire = $this->questionnaireRepo->findOneBy(["type" => $questionnaire["type"]]);

        if (!$db_questionnaire) {
            $db_questionnaire = new Questionnaire();
            $db_questionnaire->setType($questionnaire["type"]);
        }

        $aspects = $questionnaire["aspects"];

        $this->em->persist($db_questionnaire);
        $this->em->flush();

        foreach ($aspects as $aspect) {
            $isDeprecated = $aspect["deprecated"] ?? false;

            $this->importAspect($aspect, $db_questionnaire, $isDeprecated);
        }
    }

    private function importAspect($aspect, Questionnaire $questionnaire, $isDeprecated = false)
    {
        $db_aspect = $this->aspectRepo->findOneBy([
            "questionnaire" => $questionnaire->getId(),
            "local_id" => $aspect["id"]
        ]);

        if (!$db_aspect) {
            $db_aspect = new Aspect();
            $db_aspect->setCreatedAt(new \DateTimeImmutable("now"));
            $db_aspect->setLocalId($aspect["id"]);
        }

        if ($isDeprecated) {
            $db_aspect->setDeletedAt(new \DateTimeImmutable("now"));
            $this->em->persist($db_aspect);
            return;
        }

        $db_aspect->setNameDe($aspect["name_de"]);
        $db_aspect->setNameFr($aspect["name_fr"]);
        $db_aspect->setNameIt($aspect["name_it"]);
        $db_aspect->setDescriptionDe(array_key_exists("description_de", $aspect) ? $aspect["description_de"] : "");
        $db_aspect->setDescriptionFr(array_key_exists("description_fr", $aspect) ? $aspect["description_fr"] : "");
        $db_aspect->setDescriptionIt(array_key_exists("description_it", $aspect) ? $aspect["description_it"] : "");
        $db_aspect->setQuestionnaire($questionnaire);

        $questions = $aspect["questions"];

        foreach ($questions as $question) {
            $questionIsDeprecated = $question["deprecated"] ?? false;

            $this->importQuestion($question, $db_aspect, $questionIsDeprecated);
        }

        $this->em->persist($db_aspect);
        $this->em->flush();
    }

    private function importQuestion($question, Aspect $aspect, $isDeprecated)
    {
        $db_question = null;
        if ($aspect->getId() !== null) {
            $db_question = $this->questionRepo->findOneBy([
                "aspect" => $aspect->getId(),
                "local_id" => $question["id"]
            ]);
        }

        if (!$db_question) {
            $db_question = new Question();
            $db_question->setCreatedAt(new \DateTimeImmutable("now"));
            $db_question->setAnswerOptions($question["answer_options"]);
            $db_question->setLocalId($question["id"]);
        }

        if ($isDeprecated) {
            $db_question->setDeletedAt(new \DateTimeImmutable("now"));
            $this->em->persist($db_question);
            return;
        }

        $db_question->setQuestionDe($question["question_de"]);
        $db_question->setQuestionFr($question["question_fr"]);
        $db_question->setQuestionIt($question["question_it"]);
        $db_question->setAspect($aspect);

        $this->em->persist($db_question);

        $help = $question["help"];

        if (!array_key_exists("help_de", $help)) {
            // list of help
            foreach ($help as $helpItem) {
                $helpItemIsDeprecated = $isDeprecated;
                if (array_key_exists("deprecated", $helpItem)) {
                    $helpItemIsDeprecated = $helpItem["deprecated"];
                }

                $this->importHelp($helpItem, $db_question, $helpItemIsDeprecated);
            }
        } else {
            // single help
            $this->importHelp($help, $db_question, false);
        }
    }

    private function importHelp($helpItem, Question $question, $isDeprecated)
    {
        $db_help = null;
        if ($question->getId() !== null) {
            $db_help = $this->helpRepo->findOneBy([
                "question" => $question->getId(),
                "severity" => array_key_exists("severity", $helpItem) ? $helpItem["severity"] : 1
            ]);
        }

        if (!$db_help) {
            $db_help = new Help();
            $db_help->setCreatedAt(new \DateTimeImmutable("now"));
            if (array_key_exists("severity", $helpItem)) {
                $db_help->setSeverity($helpItem["severity"]);
            } else {
                $db_help->setSeverity(1);
            }
        }

        if ($isDeprecated) {
            $db_help->setDeletedAt(new \DateTimeImmutable("now"));
            $this->em->persist($db_help);
            return;
        }

        $db_help->setHelpDe($helpItem["help_de"]);
        $db_help->setHelpFr($helpItem["help_fr"]);
        $db_help->setHelpIt($helpItem["help_it"]);
        $db_help->setQuestion($question);

        if (array_key_exists("links_de", $helpItem)) {
            $linksDe = $helpItem["links_de"];
            foreach ($linksDe as $link) {
                $db_link = new Link();
                $db_link->setName($link["name"]);
                $db_link->setUrl($link["url"]);

                $this->em->persist($db_link);

                $db_help->addLinksDe($db_link);
            }
        }

        if (array_key_exists("links_fr", $helpItem)) {
            $linksFr = $helpItem["links_fr"];
            foreach ($linksFr as $link) {
                $db_link = new Link();
                $db_link->setName($link["name"]);
                $db_link->setUrl($link["url"]);

                $this->em->persist($db_link);

                $db_help->addLinksFr($db_link);
            }
        }

        if (array_key_exists("links_it", $helpItem)) {
            $linksIt = $helpItem["links_it"];
            foreach ($linksIt as $link) {
                $db_link = new Link();
                $db_link->setName($link["name"]);
                $db_link->setUrl($link["url"]);

                $this->em->persist($db_link);

                $db_help->addLinksIt($db_link);
            }
        }

        $this->em->persist($db_help);
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics(0, '');
    }
}
