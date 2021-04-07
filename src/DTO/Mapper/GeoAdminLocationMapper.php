<?php


namespace App\DTO\Mapper;


use App\DTO\Model\GeoAdminLocationDTO;

class GeoAdminLocationMapper
{
    /**
     * @param array $data
     * @return GeoAdminLocationDTO
     */
    public static function createFromArray(array $data): GeoAdminLocationDTO
    {
        $dto = new GeoAdminLocationDTO();
        $dto->setLongitude($data["attrs"]["lon"]);
        $dto->setLatitude($data["attrs"]["lat"]);
        $dto->setLabel($data["attrs"]["label"]);
        return $dto;
    }
}
