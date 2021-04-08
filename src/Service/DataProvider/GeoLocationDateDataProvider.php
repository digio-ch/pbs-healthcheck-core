<?php


namespace App\Service\DataProvider;


use App\DTO\Model\GeoLocationTypeDTO;
use App\DTO\Model\GeoLocationDTO;
use App\Entity\Group;

class GeoLocationDateDataProvider extends WidgetDataProvider
{
    /**
     * @param Group $group
     * @param string $date
     * @param array $subGroupTypes
     * @param array $peopleTypes
     * @return array|GeoLocationDTO[]
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes): array
    {
        // todo remove placeholder
        $data1Type = new GeoLocationTypeDTO();
        $data1Type->setShape("circle");
        $data1Type->setColor("#00cc00");
        $data1 = new GeoLocationDTO();
        $data1->setLongitude(8.5);
        $data1->setLatitude(47.2);
        $data1->setLabel("idk street 12");
        $data1->setType($data1Type);

        $data2Type = new GeoLocationTypeDTO();
        $data2Type->setShape("circle");
        $data2Type->setColor("#0000cc");
        $data2 = new GeoLocationDTO();
        $data2->setLongitude(8.5);
        $data2->setLatitude(47.2);
        $data2->setLabel("some street 77");
        $data2->setType($data2Type);

        $data3Type = new GeoLocationTypeDTO();
        $data3Type->setShape("meeting");
        $data3Type->setColor("#cc0000");
        $data3 = new GeoLocationDTO();
        $data3->setLongitude(8.5);
        $data3->setLatitude(47.2);
        $data3->setLabel("super street 5");
        $data3->setType($data3Type);

        return [$data1, $data2, $data3];
    }
}
