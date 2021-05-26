<?php

namespace App\Command;

use App\DTO\Model\AddressMappingDTO;
use App\Entity\Person;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use App\Repository\GeoAddressRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        $outputFile = fopen('data/address_mapping.csv', 'w');
        fwrite($outputFile, 'midata_address;midata_zip;midata_town;street;house_number;corrected_street;normalized_street;code;' . PHP_EOL);

        /** @var Person $person */
        foreach ($this->personRepository->findAll() as $person) {
            $total++;

            $addressMappingDTO = new AddressMappingDTO();
            $addressMappingDTO->setMidataAddress($person->getAddress());
            $addressMappingDTO->setMidataZip($person->getZip());
            $addressMappingDTO->setMidataTown($person->getTown());

            // person needs a complete address for the mapping
            if (
                is_null($person->getAddress()) || $person->getAddress() === '' ||
                is_null($person->getZip()) || $person->getZip() == 0 ||
                is_null($person->getTown()) || $person->getTown() === ''
            ) {
                $addressMappingDTO->setCode(AddressMappingDTO::ERROR_MISSING_ATTRIBUTE);
                $this->writeData($outputFile, $addressMappingDTO);
                continue;
            }

            // check if address has street and house number
            $address = trim($person->getAddress());

            if (!preg_match('/^([\D]*[\D][\s])+(\d+[a-z]?)$/i', $address)) {
                $addressMappingDTO->setCode(AddressMappingDTO::ERROR_INVALID_ADDRESS);
                $this->writeData($outputFile, $addressMappingDTO);
                continue;
            }

            // read house number
            $matches = array();
            preg_match('/(\d+[a-z]?)/i', $address, $matches);
            $houseNumber = $matches[0];
            $addressMappingDTO->setHouseNumber($houseNumber);

            // split and recombine the street
            $address = preg_replace('/(\d+[a-z]?)/i', '', $address);
            $addressMappingDTO->setStreetWithoutNumber($address);

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
            $addressMappingDTO->setCorrectedStreet($street);

            // normalising street
            $street = preg_replace('/str\.?(?!asse)/i', 'strasse', $street);
            $street = preg_replace('/((terr\.?(?!asse))|terasse)/i', 'terrasse', $street);
            $street = preg_replace('/(\(.*\))/i', '', $street);
            $street = preg_replace('/(?![a-zäöüèéà])/i', '', $street);
            $addressMappingDTO->setNormalizedStreet($street);

            // check if there is even a street remaining
            if (strlen($street) == 0) {
                $addressMappingDTO->setCode(AddressMappingDTO::ERROR_NORMALIZING_ERROR);
                $this->writeData($outputFile, $addressMappingDTO);
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
                $addressMappingDTO->setCode(AddressMappingDTO::ERROR_NO_GEO_LOCATION);
                $this->writeData($outputFile, $addressMappingDTO);
                continue;
            }
            $addressMappingDTO->setCode(AddressMappingDTO::STATUS_SUCCESS);
            $this->writeData($outputFile, $addressMappingDTO);

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

    private function writeData($file, AddressMappingDTO $addressMappingDTO)
    {
        fwrite(
            $file,
            sprintf(
                '%s;%d;%s;%s;%s;%s;%s;%s;',
                $addressMappingDTO->getMidataAddress(),
                $addressMappingDTO->getMidataZip(),
                $addressMappingDTO->getMidataTown(),
                $addressMappingDTO->getStreetWithoutNumber(),
                $addressMappingDTO->getHouseNumber(),
                $addressMappingDTO->getCorrectedStreet(),
                $addressMappingDTO->getNormalizedStreet(),
                $addressMappingDTO->getCode()
            ) . PHP_EOL
        );
    }
}
