<?php


namespace App\Service\DataProvider;


use App\DTO\Model\GeoLocationTypeDTO;
use App\DTO\Model\GeoLocationDTO;
use App\Entity\Group;
use App\Entity\WidgetGeoLocation;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetGeoLocationRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class GeoLocationDateDataProvider extends WidgetDataProvider
{
    /** @var WidgetGeoLocationRepository $geoLocationRepository */
    private $geoLocationRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        WidgetGeoLocationRepository $geoLocationRepository,
        TranslatorInterface $translator
    ) {
        parent::__construct($groupRepository, $groupTypeRepository, $translator);

        $this->geoLocationRepository = $geoLocationRepository;
    }

    /**
     * @param Group $group
     * @param string $date
     * @param array $subGroupTypes
     * @param array $peopleTypes
     * @return array|GeoLocationDTO[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes): array
    {
        $result = [];

        foreach ($subGroupTypes as $groupType) {
            $geoLocations = $this->geoLocationRepository->findAllForDateAndGroupType(
                $date,
                $groupType,
                $group->getId()
            );

            foreach ($geoLocations as $geoLocation) {
                $dto = new GeoLocationDTO();
                $dto->setLongitude($geoLocation['longitude']);
                $dto->setLatitude($geoLocation['latitude']);
                $dto->setLabel($geoLocation['label']);

                $dtoType = new GeoLocationTypeDTO();
                $dtoType->setShape($geoLocation['shape']);
                $color = self::GROUP_TYPE_COLORS[$geoLocation['group_type']];
                if ($color) {
                    $dtoType->setColor($color);
                }

                $dto->setType($dtoType);

                $result[] = $dto;
            }
        }

        return $result;
    }
}
