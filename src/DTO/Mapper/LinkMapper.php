<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Quap\LinkDTO;
use App\Entity\Quap\Link;

class LinkMapper
{
    public static function createLinkFromEntity(Link $link): LinkDTO
    {
        $dto = new LinkDTO();

        $dto->setName($link->getName());
        $dto->setUrl($link->getUrl());

        return $dto;
    }
}
