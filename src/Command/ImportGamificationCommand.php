<?php

namespace App\Command;

use App\Entity\Gamification\Goal;
use App\Entity\Gamification\Level;
use App\Entity\Gamification\LevelAccess;
use App\Model\CommandStatistics;
use App\Repository\Gamification\GoalRepository;
use App\Repository\Gamification\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $start = microtime(true);
        $json = json_decode(file_get_contents($this->pathToJson), true);

        $this->em->getConnection()->executeQuery('DELETE FROM hc_gamification_person_profile');
        $this->em->getConnection()->executeQuery('DELETE FROM hc_gamification_goal');
        $this->em->getConnection()->executeQuery('DELETE FROM hc_gamification_level_up_log');
        $this->em->getConnection()->executeQuery('DELETE FROM hc_gamification_level');
        $this->em->getConnection()->executeQuery('DELETE FROM hc_gamification_level_access');

        if (is_null($json['level_access'])) {
            $output->writeln('No level access requirements found.');
            return 1;
        }
        $output->writeln('importing level accesses');
        $accessLevelDBIdLookUp = $this->importLevelAccess($json['level_access'], $output);

        if (is_null($json['levels'])) {
            $output->writeln('No levels found.');
            return 1;
        }
        $output->writeln('importing levels');
        $this->importLevels($json['levels'], $accessLevelDBIdLookUp, $output);

        if (is_null($json["goals"])) {
            $output->writeln('No goals found.');
            return 1;
        }
        $this->importGoals($json['goals'], $output);

        $this->duration = microtime(true) - $start;
        return 0;
    }

    /**
     * Imports the level access and returns a map to convert the json access level id to the DB id.
     * @param array $jsonLevelAccesses
     * @param OutputInterface $output
     * @return array[string]int
     */
    protected function importLevelAccess(array $jsonLevelAccesses, OutputInterface $output): array
    {
        $jsonIDToDBId = [];

        foreach ($jsonLevelAccesses as $jsonLevelAccess) {
            $output->writeln("Creating " . $jsonLevelAccess["de_description"] . " (" . $jsonLevelAccess["id"] . ")");
            $access = new LevelAccess();
            $access->setDeDescription($jsonLevelAccess["de_description"]);
            $access->setFrDescription($jsonLevelAccess["fr_description"]);
            $access->setItDescription($jsonLevelAccess["it_description"]);

            $this->em->persist($access);
            $jsonIDToDBId[$jsonLevelAccess["id"]] = $access->getId();
        }
        $this->em->flush();

        return $jsonIDToDBId;
    }

    protected function importLevels(array $jsonLevels, array $levelAccessJsonToDBId, OutputInterface $output)
    {
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
            if (!is_null($jsonLevel["access_id"])) {
                $id = $levelAccessJsonToDBId[$jsonLevel["access_id"]];
                if (is_null($id)) {
                    throw new \Exception("access id " . $jsonLevel["access_id"] . " not found");
                }
                $reference = $this->em->getReference(LevelAccess::class, $id);
                $level->setAccess($reference);
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

    protected function importGoals(array $jsonGoals, OutputInterface $output)
    {
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
