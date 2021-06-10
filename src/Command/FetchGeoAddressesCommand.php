<?php

namespace App\Command;

use App\Entity\GeoAddress;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use App\Repository\GeoAddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchGeoAddressesCommand extends StatisticsCommand
{
    private const COORDINATION_EASTERN = 8;
    private const COORDINATION_NORTHERN = 9;
    private const ADDRESS_STREET = 13;
    private const ADDRESS_NUMBER = 14;
    private const ADDRESS_ZIP = 16;
    private const ADDRESS_TOWN = 18;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var GeoAddressRepository $geoLocationRepository */
    private $geoLocationRepository;

    /** @var PersonRepository $personRepository */
    private $personRepository;

    /** @var float */
    private $stats;

    public function __construct(
        EntityManagerInterface $em,
        GeoAddressRepository $geoLocationRepository,
        PersonRepository $personRepository
    ) {
        parent::__construct();

        $this->em = $em;
        $this->geoLocationRepository = $geoLocationRepository;
        $this->personRepository = $personRepository;

        $this->stats = 0;
    }

    protected function configure()
    {
        $this
            ->setName("app:import-geo-addresses")
            ->addOption("overwrite", null, InputArgument::OPTIONAL, "", false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException|\Doctrine\DBAL\Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        if (!file_exists('data')) {
            mkdir('data');
        }

        $overwrite = $input->getOption("overwrite");
        if ($overwrite) {
            $output->writeln(['Clearing geo locations from db']);

            $this->geoLocationRepository->wipe();
        }

        $output->writeln(['Start geo location import...']);

        $this->downloadCurrentZip($output);

        $this->readDataContent($output);

        $timeElapsed = microtime(true) - $start;
        $this->stats = $timeElapsed;

        return 0;
    }

    /**
     * @param OutputInterface $output
     */
    private function downloadCurrentZip(OutputInterface $output): void
    {
        $output->writeln(['Downloading the most recent geo data...']);

        $start = microtime(true);

        file_put_contents(
            "data/geo-data.zip",
            file_get_contents("https://data.geo.admin.ch/ch.bfs.gebaeude_wohnungs_register/CSV/CH/CH.zip")
        );

        $time = microtime(true) - $start;

        $output->writeln(['Downloaded geo data in: ' . number_format($time, 2) . ' seconds']);
    }

    /**
     * @param OutputInterface $output
     */
    private function readDataContent(OutputInterface $output): void
    {
        $file = fopen("zip://data/geo-data.zip#CH.csv", "r");

        $start = microtime(true);
        $rowStart = microtime(true);

        $output->writeln(['Caching geo locations in the db...']);

        $sqlLogger = $this->em->getConnection()->getConfiguration()->getSQLLogger();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        if ($file) {
            $index = 0;

            // skip first row
            fgets($file);

            while (!feof($file)) {
                $row = explode(';', fgets($file));

                $coordination = $this->ch1903ToWgs84(
                    floatval($row[self::COORDINATION_EASTERN]),
                    floatval($row[self::COORDINATION_NORTHERN]),
                    0
                );

                $geoLocation = new GeoAddress();
                $geoLocation->setLongitude($coordination[0]);
                $geoLocation->setLatitude($coordination[1]);
                $geoLocation->setAddress(strtolower($row[self::ADDRESS_STREET]));
                $geoLocation->setHouse(strtolower($row[self::ADDRESS_NUMBER]));
                $geoLocation->setZip(intval($row[self::ADDRESS_ZIP]));
                $geoLocation->setTown(strtolower($row[self::ADDRESS_TOWN]));

                $this->em->persist($geoLocation);

                $index++;

                // flush data every 1000 entries
                if ($index % 1000 == 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                // log process every 500k entries
                if ($index % 500000 == 0) {
                    $rowTime = microtime(true) - $rowStart;
                    $output->writeln(['Imported 500 thousand (additional) geo locations in: ' . number_format($rowTime, 2) . 's']);
                    $rowStart = microtime(true);
                }
            }

            $this->em->flush();
            $this->em->clear();

            $time = microtime(true) - $start;
            $output->writeln(['Imported ' . $index . ' geo locations in: ' . number_format($time, 2) . ' seconds']);

            fclose($file);
        }

        $this->em->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);
    }

    /**
     * @param float $east
     * @param float $north
     * @param float $height
     * @return array
     */
    private function ch1903ToWgs84(float $east, float $north, float $height): array
    {
        // Convert origin to "civil" system, where Bern has coordinates 0,0.
        $east -= 600000;
        $north -= 200000;

        // Converting CH1903+ to CH1903
        $east -= 2E6;
        $north -= 1E6;

        // Express distances in 1000km units.
        $east /= 1E6;
        $north /= 1E6;

        // Calculate longitude in 10000" units.
        $lon = 2.6779094;
        $lon += 4.728982 * $east;
        $lon += 0.791484 * $east * $north;
        $lon += 0.1306 * $east * $north * $north;
        $lon -= 0.0436 * $east * $east * $east;

        // Calculate latitude in 10000" units.
        $lat = 16.9023892;
        $lat += 3.238272 * $north;
        $lat -= 0.270978 * $east * $east;
        $lat -= 0.002528 * $north * $north;
        $lat -= 0.0447 * $east * $east * $north;
        $lat -= 0.0140 * $north * $north * $north;

        // Convert height [m].
        $height += 49.55;
        $height -= 12.60 * $east;
        $height -= 22.64 * $north;

        // Convert longitude and latitude back in degrees.
        $lon *= 100 / 36;
        $lat *= 100 / 36;

        return [$lon, $lat, $height];
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->stats, '');
    }
}
