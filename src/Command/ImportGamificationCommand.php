<?php

namespace App\Command;

use App\Entity\Gamification\Goal;
use App\Entity\Gamification\Level;
use App\Entity\Gamification\LevelAccess;
use App\Model\CommandStatistics;
use App\Repository\Gamification\GoalRepository;
use App\Repository\Gamification\LevelAccessRepository;
use App\Repository\Gamification\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportGamificationCommand extends StatisticsCommand
{
    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private LevelAccessRepository $levelAccessRepository;

    private LevelRepository $levelRepository;

    private GoalRepository $goalRepository;

    private float $duration = 0;

    private string $pathToJson = 'imports/gamification.json';

    public function __construct(
        EntityManagerInterface $em,
        LevelAccessRepository $levelAccessRepository,
        LevelRepository $levelRepository,
        GoalRepository $goalRepository
    ) {
        parent::__construct();
        $this->em = $em;
        $this->levelAccessRepository = $levelAccessRepository;
        $this->levelRepository = $levelRepository;
        $this->goalRepository = $goalRepository;
    }

    protected function configure()
    {
        $this
            ->setName('app:import-gamification');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $json = json_decode(file_get_contents($this->pathToJson), true);

        $result = $this->em->wrapInTransaction(function ($em) use ($json, $output): int {
            if (!array_key_exists('level_access', $json) || !is_array($json['level_access'])) {
                $output->writeln('No level access entries found.');
                return 1;
            }

            $output->writeln('Importing level accesses');
            $this->importLevelAccess($em, $json['level_access'], $output);

            if (!array_key_exists('levels', $json) || !is_array($json['levels'])) {
                $output->writeln('No levels found.');
                return 1;
            }

            $output->writeln("\nImporting levels");
            $this->importLevels($em, $json['levels'], $output);

            if (!array_key_exists('goals', $json) || !is_array($json['goals'])) {
                $output->writeln('No goals found.');
                return 1;
            }

            $output->writeln("\nImporting goals");
            $this->importGoals($em, $json['goals'], $output);

            return 0;
        });

        $this->duration = microtime(true) - $start;
        return $result;
    }

    /**
     * @param EntityManagerInterface $em
     * @param array $jsonLevelAccesses
     * @param OutputInterface $output
     */
    private function importLevelAccess(EntityManagerInterface $em, array $jsonLevelAccesses, OutputInterface $output)
    {
        foreach ($jsonLevelAccesses as $jsonLevelAccess) {
            $key = intval($jsonLevelAccess['key']);
            $access = $this->levelAccessRepository->findOneBy(['key' => $key]);

            $this->logEntityChange($output, "$key", $jsonLevelAccess['de_description'], $access !== null);

            if (is_null($access)) {
                $access = new LevelAccess();
                $access->setKey($key);
            }

            $access->setDeDescription($jsonLevelAccess['de_description']);
            $access->setFrDescription($jsonLevelAccess['fr_description']);
            $access->setItDescription($jsonLevelAccess['it_description']);

            $em->persist($access);
        }

        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     * @param array $jsonLevels
     * @param OutputInterface $output
     * @return void
     */
    private function importLevels(EntityManagerInterface $em, array $jsonLevels, OutputInterface $output)
    {
        foreach ($jsonLevels as $jsonLevel) {
            $key = intval($jsonLevel['key']);
            $level = $this->levelRepository->findOneBy(['key' => $key]);

            $this->logEntityChange($output, "$key", $jsonLevel['de_title'], $level !== null);

            if (is_null($level)) {
                $level = new Level();
                $level->setKey($key);
            }

            $levelAccess = null;
            $rawNextKey = $jsonLevel['next_key'];

            $rawAccessLevelKey = $jsonLevel['access_key'];

            if (!is_null($rawAccessLevelKey)) {
                $levelAccess = $this->levelAccessRepository->findOneBy(['key' => intval($rawAccessLevelKey)]);
            }

            $level->setAccess($levelAccess);
            $level->setNextKey($rawNextKey ? intval($rawNextKey) : null);
            $level->setType($jsonLevel['type']);
            $level->setRequired($jsonLevel['required']);

            $level->setDeTitle($jsonLevel['de_title']);
            $level->setFrTitle($jsonLevel['fr_title']);
            $level->setItTitle($jsonLevel['it_title']);

            $em->persist($level);
        }
        $em->flush();
    }

    private function importGoals(EntityManagerInterface $em, array $jsonGoals, OutputInterface $output)
    {
        foreach ($jsonGoals as $jsonGoal) {
            $key = $jsonGoal['key'];
            $goal = $this->goalRepository->findOneBy(['key' => $key]);

            $this->logEntityChange($output, $key, $jsonGoal['de']['title'], $goal !== null);

            if (is_null($goal)) {
                $goal = new Goal();
                $goal->setKey($key);
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

            $em->persist($goal);
        }

        $em->flush();
    }

    /**
     * @param OutputInterface $output
     * @param string $id
     * @param string $name
     * @param bool $updating whether the entity is being created or updated
     * @return void
     */
    private function logEntityChange(OutputInterface $output, string $id, string $name, bool $updating)
    {
        $action = $updating ? 'Updating' : 'Creating';

        $output->writeln("$action \"$name\" ($id)");
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->duration, '');
    }
}
