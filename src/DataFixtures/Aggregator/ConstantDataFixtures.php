<?php

namespace App\DataFixtures\Aggregator;

use App\Entity\CampState;
use App\Entity\EventType;
use App\Entity\EventTypeQualificationType;
use App\Entity\GroupType;
use App\Entity\PersonEventType;
use App\Entity\QualificationType;
use App\Entity\Role;
use App\Entity\YouthSportType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\Persistence\ObjectManager;
use JsonMachine\JsonMachine;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConstantDataFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * ConstantDataFixtures constructor.
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
        $this->importRoleTypes($manager);
        $this->importGroupTypes($manager);
        $this->importQualificationTypes($manager);
        $this->importEventTypes($manager);
        $this->importYouthSportTypes($manager);
        $this->importCampStates($manager);
        $this->importPersonEventTypes($manager);
    }

    /**
     * @param ObjectManager $em
     */
    private function importRoleTypes(ObjectManager $em)
    {
        $rolesTypes = JsonMachine::fromFile($this->params->get('test_data_dir') . '/constant/role_types.json');
        foreach ($rolesTypes as $roleType) {
            $role = new Role();
            $role->setLayerType($roleType['layer_type']);
            $role->setGroupType($roleType['group_type']);
            $role->setRoleType($roleType['role_type']);
            $role->setDeLabel($roleType['label_de']);
            $role->setFrLabel($roleType['label_fr']);
            $role->setItLabel($roleType['label_it']);

            $em->persist($role);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     */
    private function importGroupTypes(ObjectManager $em)
    {
        $groupTypes = JsonMachine::fromFile($this->params->get('test_data_dir') . '/constant/group_types.json');
        foreach ($groupTypes as $type) {
            $groupType = new GroupType();
            $groupType->setGroupType($type['group_type']);
            $groupType->setDeLabel($type['label_de']);
            $groupType->setItLabel($type['label_it']);
            $groupType->setFrLabel($type['label_fr']);

            $em->persist($groupType);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     */
    private function importQualificationTypes(ObjectManager $em)
    {
        $qualificationType = JsonMachine::fromFile(
            $this->params->get('test_data_dir') . '/constant/qualification_kinds.json'
        );
        foreach ($qualificationType as $type) {
            $qualificationType = new QualificationType();
            $qualificationType->setId($type['id']);
            $qualificationType->setValidity($type['validity']);
            $qualificationType->setDeLabel($type['label_de']);
            $qualificationType->setItLabel($type['label_it']);
            $qualificationType->setFrLabel($type['label_fr']);

            $metadata = $em->getClassMetaData(get_class($qualificationType));
            $metadata->setIdGenerator(new AssignedGenerator());

            $em->persist($qualificationType);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     */
    private function importEventTypes(ObjectManager $em)
    {
        $eventTypes = JsonMachine::fromFile($this->params->get('test_data_dir') . '/constant/event_kinds.json');
        foreach ($eventTypes as $type) {
            $eventType = new EventType();
            $eventType->setId($type['id']);
            $eventType->setDeLabel($type['label_de']);
            $eventType->setItLabel($type['label_it']);
            $eventType->setFrLabel($type['label_fr']);

            if ($type['event_kind_qualification_kinds']) {
                foreach ($type['event_kind_qualification_kinds'] as $qt) {
                    $eventTypeQualificationType = new EventTypeQualificationType();
                    $eventTypeQualificationType->setRole($qt['role']);
                    $eventTypeQualificationType->setCategory($qt['category']);
                    $eventTypeQualificationType->setEventType($eventType);

                    $qtEntity = $em->getRepository(QualificationType::class)->find($qt['qualification_kind_id']);
                    $eventTypeQualificationType->setQualificationType($qtEntity);

                    $em->persist($eventTypeQualificationType);
                }
            }

            $metadata = $em->getClassMetaData(get_class($eventType));
            $metadata->setIdGenerator(new AssignedGenerator());

            $em->persist($eventType);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     */
    private function importYouthSportTypes(ObjectManager $em)
    {
        $ageSportTypes = JsonMachine::fromFile($this->params->get('test_data_dir') . '/constant/j_s_kinds.json');
        foreach ($ageSportTypes as $type) {
            $ageSportType = new YouthSportType();
            $ageSportType->setType($type['j_s_kind']);
            $ageSportType->setDeLabel($type['label_de']);
            $ageSportType->setItLabel($type['label_it']);
            $ageSportType->setFrLabel($type['label_fr']);

            $em->persist($ageSportType);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     */
    private function importCampStates(ObjectManager $em)
    {
        $campStates = JsonMachine::fromFile($this->params->get('test_data_dir') . '/constant/camp_states.json');
        foreach ($campStates as $state) {
            $campState = new CampState();
            $campState->setState($state['state']);
            $campState->setDeLabel($state['label_de']);
            $campState->setItLabel($state['label_it']);
            $campState->setFrLabel($state['label_fr']);

            $em->persist($campState);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     */
    private function importPersonEventTypes(ObjectManager $em)
    {
        $personEventTypes = JsonMachine::fromFile(
            $this->params->get('test_data_dir') . '/constant/participation_types.json'
        );
        foreach ($personEventTypes as $type) {
            $personEventType = new PersonEventType();
            $personEventType->setType($type['participation_type']);
            $personEventType->setDeLabel($type['label_de']);
            $personEventType->setItLabel($type['label_it']);
            $personEventType->setFrLabel($type['label_fr']);

            $em->persist($personEventType);
            $em->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['constant-data'];
    }
}
