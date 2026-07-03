<?php

namespace App\Service\DataProvider;

use Doctrine\DBAL\Exception;
use App\DTO\Model\Apps\Widgets\GeoLocationDTO;
use App\DTO\Model\Apps\Widgets\GeoLocationTypeDTO;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedGeoLocationRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class GeoLocationDateDataProvider extends WidgetDataProvider
{
    private AggregatedGeoLocationRepository $geoLocationRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        AggregatedGeoLocationRepository $geoLocationRepository,
        TranslatorInterface $translator
    ) {
        parent::__construct($groupRepository, $groupTypeRepository, $translator);

        $this->geoLocationRepository = $geoLocationRepository;
    }

    /**
     * @return array|GeoLocationDTO[]
     * @throws Exception
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes): array
    {
        $result = [];
        $groupMeetingPoints = $this->geoLocationRepository->findAllMeetingPointsForDate($date, $group->getId());
        foreach ($groupMeetingPoints as $groupMeetingPoint) {
            $result[] = $this->mapGeoLocation($groupMeetingPoint, false);
        }
        foreach ($subGroupTypes as $groupType) {
            $geoLocations = $this->geoLocationRepository->findAllForDateAndGroupType(
                $date,
                $groupType,
                $group->getId(),
                $peopleTypes
            );


            $leaders = false;
            if ($peopleTypes === [ 'leaders' ]) {
                $leaders = true;
            }

            foreach ($geoLocations as $geoLocation) {
                $result[] = $this->mapGeoLocation($geoLocation, $leaders);
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $geoLocation
     */
    private function mapGeoLocation(array $geoLocation, bool $leaders): GeoLocationDTO
    {
        $dto = new GeoLocationDTO();
        $dto->setLongitude($geoLocation['longitude']);
        $dto->setLatitude($geoLocation['latitude']);
        $dto->setLabel($geoLocation['label']);

        $dtoType = new GeoLocationTypeDTO();
        $dtoType->setShape($geoLocation['shape']);
        if ($geoLocation['person_type'] === 'leaders' && !$leaders) {
            $color = self::GROUP_TYPE_COLORS['leaders'];
        } else {
            $color = self::GROUP_TYPE_COLORS[$geoLocation['group_type']];
        }
        if (!empty($color)) {
            $dtoType->setColor($color);
        }

        $dto->setType($dtoType);
        return $dto;
    }
}
