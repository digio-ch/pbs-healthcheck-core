<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Widgets\FilterDataDTO;

class FilterDataMapper
{
    /**
     * @param array $groupTypes
     * @param string[] $dates
     * @param string $locale
     * @return FilterDataDTO
     */
    public static function createFromEntities(array $groupTypes, array $dates, string $locale): FilterDataDTO
    {
        $filterData = new FilterDataDTO();
        $groupTypeDTOs = [];

        foreach ($groupTypes as $type) {
            $groupTypeDTOs[] = GroupTypeMapper::createGroupTypeFromQueryResult($type, $locale);
        }

        $filterData->setDates($dates);
        $filterData->setGroupTypes($groupTypeDTOs);
        return $filterData;
    }

    public static function createGroupTypes(array $groupTypes, string $locale): array
    {
        $groupTypeDTOs = [];
        foreach ($groupTypes as $type) {
            $groupTypeDTOs[] = GroupTypeMapper::createGroupTypeFromQueryResult($type, $locale);
        }
        return $groupTypeDTOs;
    }
}
