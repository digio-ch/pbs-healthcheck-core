<?php

namespace App\Command;

use App\Entity\Quap\Aspect;
use App\Entity\Quap\Help;
use App\Entity\Quap\Link;
use App\Entity\Quap\Question;
use App\Entity\Quap\Questionnaire;
use App\Model\CommandStatistics;
use App\Repository\Quap\AspectRepository;
use App\Repository\Quap\HelpRepository;
use App\Repository\Quap\QuestionnaireRepository;
use App\Repository\Quap\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonMachine\JsonMachine;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportQuestionnairesCommand extends StatisticsCommand
{

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    /** @var QuestionnaireRepository $questionnaireRepo */
    private QuestionnaireRepository $questionnaireRepo;

    /** @var AspectRepository $aspectRepo */
    private AspectRepository $aspectRepo;

    /** @var QuestionRepository $questionRepo */
    private QuestionRepository $questionRepo;

    /** @var HelpRepository $helpRepo */
    private HelpRepository $helpRepo;

    /** @var string $pathToJson */
    private string $pathToJson = 'imports/questionnaire_imports.json';


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
            ->setName('app:quap:import-questionnaire');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting import of questionnaires...');

        if (!file_exists($this->pathToJson)) {
            $output->writeln('No data to import. File at ' . $this->pathToJson . ' not found.');
            return 1;
        }

        // Remove all links
        $this->em->getConnection()->executeQuery('DELETE FROM hc_quap_link');

        $questionnaires = JsonMachine::fromFile($this->pathToJson);
        foreach ($questionnaires as $questionnaire) {
            $this->importQuestionnaire($questionnaire);
        }

        $output->writeln('Questionnaire import process has finished.');
        return 0;
    }

    /**
     * @param $questionnaire
     */
    private function importQuestionnaire($questionnaire): void
    {
        $db_questionnaire = $this->questionnaireRepo->findOneBy(['type' => $questionnaire['type']]);

        if (!$db_questionnaire) {
            $db_questionnaire = new Questionnaire();
            $db_questionnaire->setType($questionnaire['type']);
        }

        $aspects = $questionnaire['aspects'];

        $this->em->persist($db_questionnaire);
        $this->em->flush();

        foreach ($aspects as $aspect) {
            $isDeprecated = $aspect['deprecated'] ?? false;

            $this->importAspect($aspect, $db_questionnaire, $isDeprecated);
        }
    }

    private function importAspect($aspect, Questionnaire $questionnaire, $isDeprecated = false)
    {
        $dbAspect = $this->aspectRepo->findOneBy([
            'questionnaire' => $questionnaire->getId(),
            'local_id' => $aspect['id']
        ]);

        if (!$dbAspect) {
            $dbAspect = new Aspect();
            $dbAspect->setCreatedAt(new \DateTimeImmutable('now'));
            $dbAspect->setLocalId($aspect['id']);
        }

        if ($isDeprecated) {
            $dbAspect->setDeletedAt(new \DateTimeImmutable('now'));
            $this->em->persist($dbAspect);
            return;
        }

        $dbAspect->setNameDe($aspect['name_de']);
        $dbAspect->setNameFr($aspect['name_fr']);
        $dbAspect->setNameIt($aspect['name_it']);
        $dbAspect->setDescriptionDe(array_key_exists('description_de', $aspect) ? $aspect['description_de'] : '');
        $dbAspect->setDescriptionFr(array_key_exists('description_fr', $aspect) ? $aspect['description_fr'] : '');
        $dbAspect->setDescriptionIt(array_key_exists('description_it', $aspect) ? $aspect['description_it'] : '');
        $dbAspect->setQuestionnaire($questionnaire);

        $questions = $aspect['questions'];

        foreach ($questions as $question) {
            $questionIsDeprecated = $question['deprecated'] ?? false;

            $this->importQuestion($question, $dbAspect, $questionIsDeprecated);
        }

        $this->em->persist($dbAspect);
        $this->em->flush();
    }

    private function importQuestion($question, Aspect $aspect, $isDeprecated)
    {
        $dbQuestion = null;
        if ($aspect->getId() !== null) {
            $dbQuestion = $this->questionRepo->findOneBy([
                'aspect' => $aspect->getId(),
                'local_id' => $question['id']
            ]);
        }

        if (!$dbQuestion) {
            $dbQuestion = new Question();
            $dbQuestion->setCreatedAt(new \DateTimeImmutable('now'));
            $dbQuestion->setAnswerOptions($question['answer_options']);
            $dbQuestion->setLocalId($question['id']);
        }

        if ($isDeprecated) {
            $dbQuestion->setDeletedAt(new \DateTimeImmutable('now'));
            $this->em->persist($dbQuestion);
            return;
        }

        $requestedAnswerOptions = $question['answer_options'];
        $currentAnswerOptions = $dbQuestion->getAnswerOptions();
        if ($currentAnswerOptions !== $requestedAnswerOptions) {
            // allow the switch between midata and non midata answer options
            if (
                ($requestedAnswerOptions === Question::ANSWER_OPTION_BINARY && $currentAnswerOptions === Question::ANSWER_OPTION_MIDATA_BINARY) ||
                ($requestedAnswerOptions === Question::ANSWER_OPTION_MIDATA_BINARY && $currentAnswerOptions === Question::ANSWER_OPTION_BINARY)
            ) {
                $dbQuestion->setAnswerOptions($requestedAnswerOptions);
            } elseif (
                ($requestedAnswerOptions === Question::ANSWER_OPTION_RANGE && $currentAnswerOptions === Question::ANSWER_OPTION_MIDATA_RANGE) ||
                ($requestedAnswerOptions === Question::ANSWER_OPTION_MIDATA_RANGE && $currentAnswerOptions === Question::ANSWER_OPTION_RANGE)
            ) {
                $dbQuestion->setAnswerOptions($requestedAnswerOptions);
            }
        }

        if ($question['evaluation_function']) {
            $dbQuestion->setEvaluationFunction($question['evaluation_function']);
        } else {
            $dbQuestion->setEvaluationFunction(null);
        }

        $dbQuestion->setQuestionDe($question['question_de']);
        $dbQuestion->setQuestionFr($question['question_fr']);
        $dbQuestion->setQuestionIt($question['question_it']);
        $dbQuestion->setAspect($aspect);

        $this->em->persist($dbQuestion);

        $help = $question['help'];

        if (!array_key_exists('help_de', $help)) {
            // list of help
            foreach ($help as $helpItem) {
                $helpItemIsDeprecated = $isDeprecated;
                if (array_key_exists('deprecated', $helpItem)) {
                    $helpItemIsDeprecated = $helpItem['deprecated'];
                }

                $this->importHelp($helpItem, $dbQuestion, $helpItemIsDeprecated);
            }
        } else {
            // single help
            $this->importHelp($help, $dbQuestion, false);
        }
    }

    private function importHelp($helpItem, Question $question, $isDeprecated)
    {
        $dbHelp = null;
        if ($question->getId() !== null) {
            $dbHelp = $this->helpRepo->findOneBy([
                'question' => $question->getId(),
                'severity' => array_key_exists('severity', $helpItem) ? $helpItem['severity'] : 1
            ]);
        }

        if (!$dbHelp) {
            $dbHelp = new Help();
            $dbHelp->setCreatedAt(new \DateTimeImmutable('now'));
            if (array_key_exists('severity', $helpItem)) {
                $dbHelp->setSeverity($helpItem['severity']);
            } else {
                $dbHelp->setSeverity(1);
            }
        }

        if ($isDeprecated) {
            $dbHelp->setDeletedAt(new \DateTimeImmutable('now'));
            $this->em->persist($dbHelp);
            return;
        }

        $dbHelp->setHelpDe($helpItem['help_de']);
        $dbHelp->setHelpFr($helpItem['help_fr']);
        $dbHelp->setHelpIt($helpItem['help_it']);
        $dbHelp->setQuestion($question);

        if (array_key_exists('links_de', $helpItem)) {
            $linksDe = $helpItem['links_de'];
            foreach ($linksDe as $link) {
                $dbLink = new Link();
                $dbLink->setName($link['name']);
                $dbLink->setUrl($link['url']);

                $this->em->persist($dbLink);

                $dbHelp->addLinksDe($dbLink);
            }
        }

        if (array_key_exists('links_fr', $helpItem)) {
            $linksFr = $helpItem['links_fr'];
            foreach ($linksFr as $link) {
                $dbLink = new Link();
                $dbLink->setName($link['name']);
                $dbLink->setUrl($link['url']);

                $this->em->persist($dbLink);

                $dbHelp->addLinksFr($dbLink);
            }
        }

        if (array_key_exists('links_it', $helpItem)) {
            $linksIt = $helpItem['links_it'];
            foreach ($linksIt as $link) {
                $dbLink = new Link();
                $dbLink->setName($link['name']);
                $dbLink->setUrl($link['url']);

                $this->em->persist($dbLink);

                $dbHelp->addLinksIt($dbLink);
            }
        }

        $this->em->persist($dbHelp);
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics(0, '');
    }
}
