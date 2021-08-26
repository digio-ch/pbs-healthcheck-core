<?php

namespace App\DTO\Mapper;

use App\DTO\Model\HelpDTO;
use App\Entity\Help;

class HelpMapper
{

    public static function createHelpFromEntity(Help $help, string $locale): HelpDTO
    {

        $dto = new HelpDTO();

        $dto->setSeverity($help->getSeverity());

        $links = [];

        switch ($locale) {
            case (str_contains($locale, "it")):
                $dto->setHelp($help->getHelpIt());
                if ($help->getLinksIt()) {
                    $links = $help->getLinksIt();
                }
                break;
            case (str_contains($locale, "fr")):
                $dto->setHelp($help->getHelpFr());
                if ($help->getLinksFr()) {
                    $links = $help->getLinksFr();
                }
                break;
            default:
                $dto->setHelp($help->getHelpDe());
                if ($help->getLinksDe()) {
                    $links = $help->getLinksDe();
                }
                break;
        }

        foreach ($links as $link) {
            $dto->addLink(LinkMapper::createLinkFromEntity($link));
        }

        return $dto;
    }
}
