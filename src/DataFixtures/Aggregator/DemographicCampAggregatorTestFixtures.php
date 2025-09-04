<?php

namespace App\DataFixtures\Aggregator;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DemographicCampAggregatorTestFixtures extends AggregatorTestFixture implements FixtureGroupInterface
{
    public const AGGREGATOR_NAME = 'demographic-camp';

    /**
     * DemographicCampAggregatorTestFixtures constructor.
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        parent::__construct($params, self::AGGREGATOR_NAME);
    }


    public function load(ObjectManager $manager)
    {
        $this->importGroups($manager);
        $this->importCamps($manager);
        $this->importPeople($manager);
        $this->importParticipations($manager);
        $this->importRoles($manager);
    }

    public static function getGroups(): array
    {
        return [self::AGGREGATOR_NAME];
    }
}
