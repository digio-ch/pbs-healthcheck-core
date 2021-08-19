<?php

namespace App\DTO\Mapper;

use App\DTO\Model\HelpDTO;
use App\Entity\Help;

class HelpMapper
{

    public static function createHelpFromEntity(Help $help, string $locale, \DateTime $dateTime): HelpDTO
    {

        $dto = new HelpDTO();

        $dto->setSeverity($help->getSeverity());

        switch ($locale) {
            case (str_contains($locale, "it")):
                $dto->setHelp($help->getHelpIt());
                break;
            case (str_contains($locale, "fr")):
                $dto->setHelp($help->getHelpFr());
                break;
            default:
                $dto->setHelp($help->getHelpDe());
                break;
        }

        return $dto;
    }
}