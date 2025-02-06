<?php

namespace App\DTO\Mapper;

use App\DTO\Model\StatusMessageDTO;
use App\Entity\General\StatusMessage;

class StatusMessageMapper
{
    public static function mapStatusBanner(StatusMessage $entity, string $lang): StatusMessageDTO
    {
        switch ($lang) {
            case "it":
                $message = $entity->getItMessage();
                break;
            case "fr":
                $message = $entity->getFrMessage();
                break;
            case "de":
            default:
                $message = $entity->getDeMessage();
        }

        return new StatusMessageDTO($entity->getSeverity(), $message);
    }
}