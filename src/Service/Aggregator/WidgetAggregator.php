<?php

namespace App\Service\Aggregator;

use App\Entity\Midata\PersonRole;
use App\Repository\Aggregated\AggregatedEntityRepository;
use App\Repository\Midata\GroupRepository;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * This class was part of the legacy aggregators and is retained
 * for services that still reference its attributes.
 *
 * The aggregator logic has been moved to the Go-Importer.
 */
abstract class WidgetAggregator
{
    public static array $memberRoleTypes = [
        'Group::Pta::Mitglied',
        'Group::AbteilungsRover::Rover',
        'Group::Pio::Pio',
        'Group::Pfadi::Pfadi',
        'Group::Pfadi::Leitpfadi',
        'Group::Woelfe::Wolf',
        'Group::Woelfe::Leitwolf',
        'Group::Biber::Biber'
    ];

    public static array $leaderRoleTypesByGroupType = [
        'Group::Abteilung' => [
            'Group::Abteilung::Abteilungsleitung',
            'Group::Abteilung::AbteilungsleitungStv'
        ],
        'Group::Pta' => [
            'Group::Abteilung::StufenleitungPta',
            'Group::Pta::Einheitsleitung',
            'Group::Pta::Mitleitung'
        ],
        'Group::AbteilungsRover' => [
            'Group::Abteilung::StufenleitungRover',
            'Group::AbteilungsRover::Einheitsleitung',
            'Group::AbteilungsRover::Mitleitung'
        ],
        'Group::Pio' => [
            'Group::Abteilung::StufenleitungPio',
            'Group::Pio::Einheitsleitung',
            'Group::Pio::Mitleitung'
        ],
        'Group::Pfadi' => [
            'Group::Abteilung::StufenleitungPfadi',
            'Group::Pfadi::Einheitsleitung',
            'Group::Pfadi::Mitleitung'
        ],
        'Group::Woelfe' => [
            'Group::Abteilung::StufenleitungWoelfe',
            'Group::Woelfe::Einheitsleitung',
            'Group::Woelfe::Mitleitung'
        ],
        'Group::Biber' => [
            'Group::Abteilung::StufenleitungBiber',
            'Group::Biber::Einheitsleitung',
            'Group::Biber::Mitleitung'
        ],
    ];

    public static array $leadersRoleTypes = [
        'Group::Abteilung::StufenleitungPta',
        'Group::Abteilung::StufenleitungRover',
        'Group::Abteilung::StufenleitungPio',
        'Group::Abteilung::StufenleitungPfadi',
        'Group::Abteilung::StufenleitungWoelfe',
        'Group::Abteilung::StufenleitungBiber',
        'Group::Pta::Einheitsleitung',
        'Group::Pta::Mitleitung',
        'Group::AbteilungsRover::Einheitsleitung',
        'Group::AbteilungsRover::Mitleitung',
        'Group::Pio::Einheitsleitung',
        'Group::Pio::Mitleitung',
        'Group::Pfadi::Einheitsleitung',
        'Group::Pfadi::Mitleitung',
        'Group::Woelfe::Einheitsleitung',
        'Group::Woelfe::Mitleitung',
        'Group::Biber::Einheitsleitung',
        'Group::Biber::Mitleitung',
    ];

    public static array $mainGroupRoleTypes = [
        'Group::Abteilung::Abteilungsleitung',
        'Group::Abteilung::AbteilungsleitungStv',
    ];

    public static array $typeOrder = [
        'Group::Biber',
        'Group::Woelfe',
        'Group::Pfadi',
        'Group::Pio',
        'Group::Pta',
        'Group::AbteilungsRover',
        'Group::Abteilung'
    ];
}
