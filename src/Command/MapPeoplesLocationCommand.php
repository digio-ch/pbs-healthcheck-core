<?php


namespace App\Command;


use App\Entity\Person;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use App\Repository\GeoLocationRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Sodium\add;

class MapPeoplesLocationCommand extends StatisticsCommand
{
    /** @var PersonRepository $personRepository */
    private $personRepository;

    /** @var GeoLocationRepository $geoLocationRepository */
    private $geoLocationRepository;

    /** @var float */
    private $stats;

    public function __construct(
        PersonRepository $personRepository,
        GeoLocationRepository $geoLocationRepository
    ){
        parent::__construct();

        $this->personRepository = $personRepository;
        $this->geoLocationRepository = $geoLocationRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:map-peoples-location");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException|\Doctrine\ORM\ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $output->writeln(['Mapping people to their geo location via their address...']);

        $mapped = 0;
        $total = 0;

        /** @var Person $person */
        foreach ($this->personRepository->findAll() as $person) {
            $total++;

            // person needs a complete address for the mapping
            if (
                is_null($person->getAddress()) ||
                is_null($person->getZip()) ||
                is_null($person->getTown())
            ) {
                continue;
            }

            $addressParts = explode(" ", $person->getAddress());

            // address needs to have a street and a house number
            if ($addressParts < 2) {
                continue;
            }

            $houseNumber = $addressParts[count($addressParts) - 1];

            $street = $addressParts[0];

            // combine the address parts which belong to the street if the street consists out of more than 1 word
            for ($i = 1; $i < count($addressParts) - 1; $i++) {
                $street = $street . " " . $addressParts[$i];
            }

            $geoLocation = $this->geoLocationRepository->findOneByAddress(
                $person->getZip(),
                $person->getTown(),
                $street,
                $houseNumber
            );

            // couldn't find a geo location for this persons address
            if (is_null($geoLocation)) {
                continue;
            }

            $person->setGeoLocation($geoLocation);
            $this->personRepository->save($person);

            $mapped++;
        }

        $this->stats = microtime(true) - $start;

        $output->writeln(['Mapped ' . $mapped . ' locations to a person and skipped ' . ($total - $mapped) . ' people due to invalid addresses in: ' . number_format($this->stats, 2) . 's']);

        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->stats, '');
    }
}
