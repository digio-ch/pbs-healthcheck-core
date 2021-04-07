<?php


namespace App\Command;


use App\DTO\Mapper\GeoAdminLocationMapper;
use App\DTO\Model\GeoAdminLocationDTO;
use App\Entity\Person;
use App\Entity\WidgetGeoLocation;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use App\Repository\WidgetGeoLocationRepository;
use GuzzleHttp\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchGeoLocationsCommand extends StatisticsCommand
{
    /** @var WidgetGeoLocationRepository $geoLocationRepository */
    private $geoLocationRepository;

    /** @var PersonRepository $personRepository */
    private $personRepository;

    public function __construct(
        WidgetGeoLocationRepository $geoLocationRepository,
        PersonRepository $personRepository
    ) {
        parent::__construct();
        $this->geoLocationRepository = $geoLocationRepository;
        $this->personRepository = $personRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->downloadCurrentZip();
    }

    private function downloadCurrentZip(): void
    {
        file_get_contents(
            "geo-data.zip",
            ""
        );
    }

    private function getGeoLocation(Person $person): WidgetGeoLocation
    {
        if ($person->getGeoLocation()) {
            return $person->getGeoLocation();
        }

        $result = $this->geoLocationRepository->findOneByAddress(
            $person->getZip(),
            $person->getTown(),
            $person->getAddress()
        );

        if ($result) {
            $person->setGeoLocation($result);
            $this->personRepository->save($person);
            return $result;
        }

        $dto = $this->lookupAddress(
            $person->getZip(),
            $person->getTown(),
            $person->getAddress()
        );

        $geoLocation = new WidgetGeoLocation();
        $geoLocation->setZip($person->getZip());
        $geoLocation->setTown($person->getTown());
        $geoLocation->setAddress($person->getAddress());
        $geoLocation->setLongitude($dto->getLongitude());
        $geoLocation->setLatitude($dto->getLatitude());
        $person->setGeoLocation($geoLocation);

        $this->geoLocationRepository->save($geoLocation);

        return $geoLocation;
    }

    /**
     * @param int $zip
     * @param string $town
     * @param string $address
     * @return GeoAdminLocationDTO|null
     */
    private function lookupAddress(int $zip, string $town, string $address): ?GeoAdminLocationDTO
    {
        $searchText = $address . ", " . $zip . " " .$town;
        $searchText = str_replace(" ", "%20", $searchText);

        $client = new Client();
        $response = $client->get(
            "https://api3.geo.admin.ch/rest/services/api/SearchServer
            ?features=ch.bfs.gebaeude_wohnungs_register
            &type=featuresearch
            &searchText=" . $searchText . "
            &limit=1"
        );

        $jsonData = json_decode($response, true);

        // return the first search result
        return count($jsonData["results"]) > 0 ? GeoAdminLocationMapper::createFromArray($jsonData["results"][0]) : null;
    }

    public function getStats(): CommandStatistics
    {
        // TODO: Implement getStats() method.
    }
}
