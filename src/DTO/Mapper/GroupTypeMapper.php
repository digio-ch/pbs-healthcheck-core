<?php

namespace App\DTO\Mapper;

use App\DTO\Model\GroupTypeDTO;
use App\Entity\Midata\GroupType;
use App\Service\DataProvider\WidgetDataProvider;

class GroupTypeMapper
{
    public static function createGroupTypeFromEntity(GroupType $groupType, string $locale): GroupTypeDTO
    {
        $groupTypeDTO = new GroupTypeDTO();
        $groupTypeDTO->setId($groupType->getId());
        self::setLabelForLocale($groupTypeDTO, $groupType, $locale);
        $groupTypeDTO->setGroupType($groupType->getGroupType());
        $color = array_key_exists($groupType->getGroupType(), WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES) ?
            WidgetDataProvider::GROUP_TYPE_COLORS[$groupType->getGroupType()] : '';
        $groupTypeDTO->setColor($color);
        return $groupTypeDTO;
    }

    public static function createGroupTypeFromQueryResult(array $result, string $locale): GroupTypeDTO
    {
        $groupTypeDTO = new GroupTypeDTO();
        $groupTypeDTO->setId($result['id']);
        self::setLabelForLocaleFromQueryResult($groupTypeDTO, $result, $locale);
        $groupTypeDTO->setGroupType($result['group_type']);
        $groupTypeDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$result['group_type']]);
        return $groupTypeDTO;
    }

    private static function setLabelForLocaleFromQueryResult(GroupTypeDTO $groupTypeDTO, array $result, string $locale)
    {
        switch ($locale) {
            case str_contains($locale, 'it'):
                $groupTypeDTO->setLabel($result['it_label']);
                break;
            case str_contains($locale, 'fr'):
                $groupTypeDTO->setLabel($result['fr_label']);
                break;
            default:
                $groupTypeDTO->setLabel($result['de_label']);
        }
    }

    private static function setLabelForLocale(GroupTypeDTO $groupTypeDTO, GroupType $groupType, string $locale)
    {
        switch ($locale) {
            case str_contains($locale, 'it'):
                $groupTypeDTO->setLabel($groupType->getItLabel());
                break;
            case str_contains($locale, 'fr'):
                $groupTypeDTO->setLabel($groupType->getFrLabel());
                break;
            default:
                $groupTypeDTO->setLabel($groupType->getDeLabel());
        }
    }
}
