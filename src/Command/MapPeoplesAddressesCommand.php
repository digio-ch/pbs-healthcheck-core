<?php

namespace App\Command;

use App\DTO\Model\AddressMappingDTO;
use App\Entity\Person;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use App\Repository\GeoAddressRepository;
use App\Service\Profiler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->setName("app:map-peoples-addresses")
            ->addOption("log-level", null, InputArgument::OPTIONAL, "", 2);
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

        $outputFile = null;
        $logLevel = $input->getOption("log-level");
        if ($logLevel > 0) {
            if (!file_exists('data')) {
                mkdir('data');
            }

            $outputFile = fopen('data/address_mapping.csv', 'w');
            fwrite($outputFile, 'midata_address;midata_zip;midata_town;street;house_number;corrected_street;normalized_street;code;' . PHP_EOL);
        }

        /** @var Person $person */
        foreach ($this->personRepository->findAll() as $person) {
            $total++;

            $profiler = new Profiler($output, 'person');
            if ($this->processPerson($person, $outputFile, $output, $logLevel)) {
                $mapped++;
            }
            $profiler->endTimer();

            if ($total % 1000 === 0) {
                $profiler = new Profiler($output, "flush entities");
                $this->em->flush();
                $profiler->endTimer();

                $output->writeln(sprintf('<info>processed %d addresses</info>', $total));
            }
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

    private function processPerson(Person $person, $outputFile, OutputInterface $output, int $logLevel): bool
    {
        $addressMappingDTO = new AddressMappingDTO();
        $addressMappingDTO->setMidataAddress($person->getAddress());
        $addressMappingDTO->setMidataZip($person->getZip());
        $addressMappingDTO->setMidataTown($person->getTown());

        // person needs a complete address for the mapping
        if (
            is_null($person->getAddress()) || $person->getAddress() === '' ||
            (
                is_null($person->getZip()) || $person->getZip() == 0 &&
                is_null($person->getTown()) || $person->getTown() === ''
            )
        ) {
            $addressMappingDTO->setCode(AddressMappingDTO::ERROR_MISSING_ATTRIBUTE);
            $this->writeData($outputFile, $addressMappingDTO);
            return false;
        }

        // cleanup the town
        $town = $person->getTown() ? $person->getTown() : "";
        $town = MapPeoplesAddressesCommand::normaliseAddress($town);

        // check if address has street and house number
        $address = trim($person->getAddress());

        $profiler = new Profiler($output, "map address '" . $address . "'");

        if (!preg_match('/^([\D]*[\D][\s])+(\d+[a-z]?)$/i', $address)) {
            $addressMappingDTO->setCode(AddressMappingDTO::ERROR_INVALID_ADDRESS);
            $this->writeData($outputFile, $addressMappingDTO);
            $profiler->endTimer();
            return false;
        }

        list($street, $houseNumber) = $this->mapAddress($address, $addressMappingDTO);

        // check if there is even a street remaining
        if (strlen($street) == 0) {
            $addressMappingDTO->setCode(AddressMappingDTO::ERROR_NORMALIZING_ERROR);
            $this->writeData($outputFile, $addressMappingDTO);
            $profiler->endTimer();
            return false;
        }

        $profiler->endTimer();

        $profiler = new Profiler($output, "find address '" . $address . "'");
        $geoLocation = $this->geoLocationRepository->findIdByAddress($person->getZip(), $town, $street, $houseNumber);
        $profiler->endTimer();

        // couldn't find a geo location for this persons address
        if (!$geoLocation) {
            $addressMappingDTO->setCode(AddressMappingDTO::ERROR_NO_GEO_LOCATION);
            $this->writeData($outputFile, $addressMappingDTO);
            return false;
        }
        $addressMappingDTO->setCode(AddressMappingDTO::STATUS_SUCCESS);
        if ($logLevel >= 2) {
            $this->writeData($outputFile, $addressMappingDTO);
        }

        $profiler = new Profiler($output, "persist entity '" . $address . "'");

        $person->setGeoAddress($geoLocation);
        $this->em->persist($person);

        $profiler->endTimer();
        return true;
    }

    private function mapAddress(string $address, AddressMappingDTO $addressMappingDTO)
    {
        // read house number
        $matches = array();
        preg_match('/(\d+[a-z]?)/i', preg_replace('/\s/i', '', $address), $matches);
        $houseNumber = $matches[0];
        $addressMappingDTO->setHouseNumber($houseNumber);

        // split and recombine the street
        $street = preg_replace('/(\d+[a-z]?)/i', '', $address);
        $addressMappingDTO->setStreetWithoutNumber($street);

        // correcting street
        $street = preg_replace('/str\.?(?!asse)/i', 'strasse', $street);
        $street = preg_replace('/((terr\.?(?!asse))|terasse)/i', 'terrasse', $street);
        $street = preg_replace('/(\(.*\))/i', '', $street);
        $addressMappingDTO->setCorrectedStreet($street);

        // normalising street
        $street = MapPeoplesAddressesCommand::normaliseAddress($street);
        $addressMappingDTO->setNormalizedStreet($street);

        return [$street, $houseNumber];
    }

    private function writeData($file, AddressMappingDTO $addressMappingDTO)
    {
        if (!$file) {
            return;
        }

        fwrite(
            $file,
            sprintf(
                '%s;%d;%s;%s;%s;%s;%s;%s;',
                $addressMappingDTO->getMidataAddress() ? $addressMappingDTO->getMidataAddress() : '',
                $addressMappingDTO->getMidataZip() ? $addressMappingDTO->getMidataZip() : '',
                $addressMappingDTO->getMidataTown() ? $addressMappingDTO->getMidataTown() : '',
                $addressMappingDTO->getStreetWithoutNumber() ? $addressMappingDTO->getStreetWithoutNumber() : '',
                $addressMappingDTO->getHouseNumber() ? $addressMappingDTO->getHouseNumber() : '',
                $addressMappingDTO->getCorrectedStreet() ? $addressMappingDTO->getCorrectedStreet() : '',
                $addressMappingDTO->getNormalizedStreet() ? $addressMappingDTO->getNormalizedStreet() : '',
                $addressMappingDTO->getCode() ? $addressMappingDTO->getCode() : ''
            ) . PHP_EOL
        );
    }

    public static function normaliseAddress(string $address)
    {
        $address = strtolower($address);
        $address = preg_replace('/[éèêë]+/i', 'e', $address);
        $address = preg_replace('/[àâä]+/i', 'a', $address);
        $address = preg_replace('/[ùûü]+/i', 'u', $address);
        $address = preg_replace('/[ç]+/i', 'c', $address);
        $address = preg_replace('/[ïî]+/i', 'i', $address);

        $address = preg_replace('/(?![a-z])/i', '', $address);
        $address = preg_replace('/[\s]+/i', '', $address);

        return utf8_encode($address);
    }
}
