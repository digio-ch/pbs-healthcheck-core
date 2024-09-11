<?php

namespace App\Command;

use App\Entity\Gamification\Goal;
use App\Entity\Gamification\Level;
use App\Model\CommandStatistics;
use App\Repository\Gamification\GoalRepository;
use App\Repository\Gamification\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonMachine\JsonMachine;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportGamificationCommand extends StatisticsCommand
{

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private LevelRepository $levelRepository;

    private GoalRepository $goalRepository;

    private float $duration = 0;

    private string $pathToJson = 'imports/gamification.json';

    public function __construct(
        EntityManagerInterface $em,
        LevelRepository $levelRepository,
        GoalRepository $goalRepository
    ) {
        parent::__construct();
        $this->em = $em;
        $this->levelRepository = $levelRepository;
        $this->goalRepository = $goalRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:import-gamification");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime( true);
        $json = json_decode(file_get_contents($this->pathToJson), true);

        $this->em->getConnection()->executeQuery('DELETE FROM gamification_person_profile');
        $this->em->getConnection()->executeQuery('DELETE FROM goal');
        $this->em->getConnection()->executeQuery('DELETE FROM level');
        if (is_null($json['levels'])) {
            $output->writeln('No levels found.');
            return 1;
        }
        $output->writeln('importing levels');
        $this->importLevels($json['levels'], $output);

        if (is_null($json["goals"])) {
            $output->writeln('No goals found.');
            return 1;
        }
        $this->importGoals($json['goals'], $output);

        $this->duration = microtime(true) - $start;
        return 0;
    }

    protected function importLevels(array $jsonLevels, OutputInterface $output) {
        foreach ($jsonLevels as $jsonLevel) {
            $level = $this->levelRepository->findOneBy(["key" => $jsonLevel["key"]]);
            if (is_null($level)) {
                $level = new Level();
                $level->setKey(intval($jsonLevel["key"]));
                if (!is_null($jsonLevel["next_key"])) {
                    $level->setNextKey(intval($jsonLevel["next_key"]));
                }
                $output->writeln("Creating " . $jsonLevel["de_title"] . " (" . $jsonLevel["key"] . ")");
            }
            $level->setRequired($jsonLevel["required"]);
            $level->setType($jsonLevel["type"]);
            $level->setDeTitle($jsonLevel["de_title"]);
            $level->setFrTitle($jsonLevel["fr_title"]);
            $level->setItTitle($jsonLevel["it_title"]);

            $this->em->persist($level);
        }
        $this->em->flush();
    }

    protected function importGoals(array $jsonGoals, OutputInterface $output) {
        foreach ($jsonGoals as $jsonGoal) {
            $goal = $this->goalRepository->findOneBy(['key' => $jsonGoal['key']]);
            if (is_null($goal)) {
                $goal = new Goal();
                $goal->setKey($jsonGoal['key']);
                $output->writeln("Creating " . $jsonGoal["de"]["title"] . " (" . $jsonGoal["key"] . ")");
            }
            $level = $this->levelRepository->findOneBy(['key' => $jsonGoal['level']]);
            $goal->setLevel($level);
            $goal->setRequired($jsonGoal['required']);

            $goal->setDeTitle($jsonGoal['de']['title']);
            $goal->setDeInformation($jsonGoal['de']['information']);
            $goal->setDeHelp($jsonGoal['de']['help']);
            $goal->setFrTitle($jsonGoal['fr']['title']);
            $goal->setFrInformation($jsonGoal['fr']['information']);
            $goal->setFrHelp($jsonGoal['fr']['help']);
            $goal->setItTitle($jsonGoal['it']['title']);
            $goal->setItInformation($jsonGoal['it']['information']);
            $goal->setItHelp($jsonGoal['it']['help']);

            $this->em->persist($goal);
        }
        $this->em->flush();
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->duration, '');
    }
}
