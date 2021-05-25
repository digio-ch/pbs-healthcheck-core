<?php

namespace App\Command;

use App\Entity\Person;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use App\Repository\GeoAddressRepository;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function GuzzleHttp\Psr7\str;

class MapPeoplesAddressesCommand extends StatisticsCommand
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var PersonRepository $personRepository */
    private $personRepository;

    /** @var GeoAddressRepository $geoLocationRepository */
    private $geoLocationRepository;

    /** @var float */
    private $stats;

    public function __construct(
        EntityManagerInterface $em,
        PersonRepository $personRepository,
        GeoAddressRepository $geoLocationRepository
    ) {
        parent::__construct();

        $this->em = $em;
        $this->personRepository = $personRepository;
        $this->geoLocationRepository = $geoLocationRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:map-peoples-addresses");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $output->writeln(['Mapping people to their geo location via their address...']);

        $sqlLogger = $this->em->getConnection()->getConfiguration()->getSQLLogger();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $mapped = 0;
        $total = 0;

        /** @var Person $person */
        foreach ($this->personRepository->findAll() as $person) {
            $total++;

            $output->writeln(['----------------------------------------------']);

            $output->writeln([$person->getAddress() . ' ' . $person->getZip() . ' ' . $person->getTown()]);

            // person needs a complete address for the mapping
            if (
                is_null($person->getAddress()) || $person->getAddress() === '' ||
                is_null($person->getZip()) || $person->getZip() == 0 ||
                is_null($person->getTown()) || $person->getTown() === ''
            ) {
                $output->writeln(['error code: 1 (missing attribute)']);
                continue;
            }

            // check if address has street and house number
            $address = trim($person->getAddress());

            if (!preg_match('/^([\D]*[\D][\s])+(\d+[a-z]?)$/i', $address)) {
                $output->writeln(['error code: 2 (invalid address)']);
                continue;
            }

            // read house number
            $matches = array();
            preg_match('/(\d+[a-z]?)/i', $address, $matches);
            $houseNumber = $matches[0];
            $output->writeln(['evaluated house number: ' . $houseNumber]);

            // split and recombine the street
            $address = preg_replace('/(\d+[a-z]?)/i', '', $address);
            $output->writeln(['street without number: ' . $address]);
            $addressParts = explode(' ', trim($address));

            $street = '';

            // recombine the address parts which belong to the street if the street consists out of more than 1 word
            for ($i = 0; $i < count($addressParts); $i++) {
                $part = $addressParts[$i];
                // remove short or invalid parts
                if (strlen($part) <= 2 || strtolower($part) === 'les') {
                    continue;
                }

                // don't add space if its the first address part
                if ($street == '') {
                    $street = $addressParts[$i];
                    continue;
                }
                $street = $street . ' ' . $addressParts[$i];
            }
            $output->writeln(['corrected street: ' . $street]);

            $output->writeln(['before normalisation: ' . $street]);
            // normalising street
            $street = preg_replace('/str\.?(?!asse)/i', 'strasse', $street);
            $street = preg_replace('/((terr\.?(?!asse))|terasse)/i', 'terrasse', $street);
            $street = preg_replace('/(\(.*\))/i', '', $street);
            $output->writeln(['after normalisation: ' . $street]);

            // check if there is even a street remaining
            if (strlen($street) == 0) {
                $output->writeln(['error code: 3 (no street remaining)']);
                continue;
            }

            $geoLocation = $this->geoLocationRepository->findIdByAddress(
                $person->getZip(),
                $person->getTown(),
                $street,
                $houseNumber
            );

            // couldn't find a geo location for this persons address
            if (is_null($geoLocation) || $geoLocation === 0) {
                $output->writeln(['error code: 4 (no geo location found)']);
                continue;
            }
            $output->writeln(['status code: 0 (mapped)']);

            $this->personRepository->mapGeoAddress($person->getId(), $geoLocation);

            $mapped++;
        }

        $this->em->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);

        $this->stats = microtime(true) - $start;

        $output->writeln(['Mapped ' . $mapped . ' locations to a person and skipped ' . ($total - $mapped) . ' people due to invalid addresses in: ' . number_format($this->stats, 2) . 's']);

        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->stats, '');
    }
}
