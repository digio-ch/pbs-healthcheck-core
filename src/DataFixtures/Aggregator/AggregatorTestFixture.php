<?php

namespace App\DataFixtures\Aggregator;

use App\Entity\Midata\Camp;
use App\Entity\Midata\Event;
use App\Entity\Midata\EventDate;
use App\Entity\Midata\EventGroup;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Midata\Person;
use App\Entity\Midata\PersonEvent;
use App\Entity\Midata\PersonEventType;
use App\Entity\Midata\PersonQualification;
use App\Entity\Midata\PersonRole;
use App\Entity\Midata\QualificationType;
use App\Entity\Midata\Role;
use App\Entity\Midata\YouthSportType;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\Persistence\ObjectManager;
use Exception;
use JsonMachine\JsonMachine;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AggregatorTestFixture extends Fixture
{
    /**
     * @var string
     */
    private $aggregatorName;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * AggregatorTestFixture constructor.
     * @param string $aggregatorName
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params, string $aggregatorName = '')
    {
        $this->aggregatorName = $aggregatorName;
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
    }

    /**
     * @param ObjectManager $em
     * @throws Exception
     */
    protected function importCamps(ObjectManager $em)
    {
        $camps = JsonMachine::fromFile(
            '/' . $this->params->get('test_data_dir') . '/' . $this->aggregatorName . '/camps.json'
        );
        foreach ($camps as $c) {
            $camp = new Camp();
            $camp->setId($c['id']);
            $camp->setState($c['state']);
            $camp->setLocation($c['location']);

            if (isset($c['name'])) {
                $camp->setName($c['name']);
            }

            $ageSportType = $em->getRepository(YouthSportType::class)->findOneBy(['type' => $c['j_s_kind']]);
            $camp->setYouthSportType($ageSportType);

            if ($c['dates']) {
                foreach ($c['dates'] as $date) {
                    $eventDate = new EventDate();
                    $eventDate->setEvent($camp);
                    $eventDate->setStartAt(new DateTimeImmutable($date['start_at']));
                    $eventDate->setEndAt(new DateTimeImmutable($date['finish_at']));
                    $em->persist($eventDate);
                }
            }

            if ($c['groups']) {
                foreach ($c['groups'] as $g) {
                    $group = $em->getRepository(Group::class)->find($g['id']);
                    if ($group) {
                        $eventGroup = new EventGroup();
                        $eventGroup->setGroup($group);
                        $eventGroup->setEvent($camp);
                        $camp->addGroup($eventGroup);
                    }
                }
            }

            $metadata = $em->getClassMetaData(get_class($camp));
            $metadata->setIdGenerator(new AssignedGenerator());

            $em->persist($camp);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     * @throws Exception
     */
    protected function importGroups(ObjectManager $em)
    {
        $groups = JsonMachine::fromFile(
            '/' . $this->params->get('test_data_dir') . '/' . $this->aggregatorName . '/groups.json'
        );
        foreach ($groups as $gr) {
            $group = new Group();
            $group->setId($gr['id']);
            $group->setName($gr['name']);
            $group->setCantonId($gr['canton_id']);
            $group->setCantonName($gr['canton_name']);
            $group->setCreatedAt(new DateTimeImmutable($gr['created_at']));
            if ($gr['deleted_at']) {
                $group->setDeletedAt(new DateTimeImmutable($gr['deleted_at']));
            }

            /** @var GroupType $gt */
            $gt = $em->getRepository(GroupType::class)->findOneBy(['groupType' => $gr['type']]);
            $group->setGroupType($gt);

            if ($gr['parent_id'] !== null) {
                $pg = $em->getRepository(Group::class)->find($gr['parent_id']);
                $group->setParentGroup($pg);
            }

            $metadata = $em->getClassMetaData(get_class($group));
            $metadata->setIdGenerator(new AssignedGenerator());

            $em->persist($group);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $em
     * @throws Exception
     */
    protected function importPeople(ObjectManager $em)
    {
        $people = JsonMachine::fromFile(
            '/' . $this->params->get('test_data_dir') . '/' . $this->aggregatorName . '/people.json'
        );
        foreach ($people as $p) {
            $person = new Person();
            $person->setId($p['id']);
            $person->setNickname($p['name']);
            $person->setGender($p['gender']);
            $person->setAddress($p['address']);
            $person->setCountry($p['country']);
            $person->setZip(intval($p['zip_code']));
            if ($p['birthday']) {
                $person->setBirthday(new DateTimeImmutable($p['birthday']));
            }
            $person->setPbsNumber($p['pbs_number']);
            if ($p['entry_date']) {
                $person->setEntryDate(new DateTimeImmutable($p['entry_date']));
            }
            if ($p['leaving_date']) {
                $person->setLeavingDate(new DateTimeImmutable($p['leaving_date']));
            }
            $person->setTown($p['town']);

            if ($p['primary_group_id']) {
                $group = $em->getRepository(Group::class)->find($p['primary_group_id']);
                if ($group) {
                    $person->setGroup($group);
                }
            }

            $metadata = $em->getClassMetaData(get_class($person));
            $metadata->setIdGenerator(new AssignedGenerator());

            $em->persist($person);
        }
        $em->flush();
    }

    /**
     * @param ObjectManager $em
     */
    protected function importParticipations(ObjectManager $em)
    {
        $participations = JsonMachine::fromFile(
            '/' . $this->params->get('test_data_dir') . '/' . $this->aggregatorName . '/participations.json'
        );
        foreach ($participations as $participation) {
            $personEvent = new PersonEvent();
            $person = $em->getRepository(Person::class)->find($participation['person_id']);
            $event = $em->getRepository(Event::class)->find($participation['event_id']);
            $personEvent->setQualified($participation['qualified']);
            $personEvent->setId($participation['id']);

            if ($participation['roles']) {
                foreach ($participation['roles'] as $role) {
                    $personEventType = $em->getRepository(PersonEventType::class)->findOneBy(
                        ['type' => $role['type']]
                    );
                    if ($personEventType) {
                        $personEvent->addPersonEventType($personEventType);
                    }
                }
            }

            if ($person && $event) {
                $personEvent->setPerson($person);
                $personEvent->setEvent($event);

                $metadata = $em->getClassMetaData(get_class($personEvent));
                $metadata->setIdGenerator(new AssignedGenerator());

                $em->persist($personEvent);
            }
        }
        $em->flush();
    }

    /**
     * @param ObjectManager $em
     * @throws Exception
     */
    protected function importQualifications(ObjectManager $em)
    {
        $qualifications = JsonMachine::fromFile(
            '/' . $this->params->get('test_data_dir') . '/' . $this->aggregatorName . '/qualifications.json'
        );
        foreach ($qualifications as $qualification) {
            $personQualification = new PersonQualification();
            $personQualification->setEventOrigin($qualification['origin']);
            $personQualification->setId($qualification['id']);

            $personQualification->setStartAt(new DateTimeImmutable($qualification['start_at']));
            $personQualification->setEndAt(new DateTimeImmutable($qualification['finish_at']));

            $person = $em->getRepository(Person::class)->find($qualification['person_id']);
            $qualificationType = $em->getRepository(QualificationType::class)->find(
                $qualification['qualification_kind_id']
            );
            if ($person) {
                $personQualification->setPerson($person);
            }

            if ($qualificationType) {
                $personQualification->setQualificationType($qualificationType);
            }

            $metadata = $em->getClassMetaData(get_class($personQualification));
            $metadata->setIdGenerator(new AssignedGenerator());

            $em->persist($personQualification);
        }
        $em->flush();
    }

    /***
     * @param ObjectManager $em
     * @throws Exception
     */
    protected function importRoles(ObjectManager $em)
    {
        $roles = JsonMachine::fromFile(
            '/' . $this->params->get('test_data_dir') . '/' . $this->aggregatorName . '/roles.json'
        );
        foreach ($roles as $r) {
            $personRole = new PersonRole();
            $personRole->setId($r['id']);
            $person = $em->getRepository(Person::class)->find($r['person_id']);
            if ($person) {
                $personRole->setPerson($person);
            }

            $role = $em->getRepository(Role::class)->getOneByRoleType($r['type']);
            if ($role) {
                $personRole->setRole($role);
            }

            $group = $em->getRepository(Group::class)->find($r['group_id']);
            if ($group) {
                $personRole->setGroup($group);
            }

            $personRole->setCreatedAt(new DateTimeImmutable($r['created_at']));
            if ($r['deleted_at']) {
                $personRole->setDeletedAt(new DateTimeImmutable($r['deleted_at']));
            }

            $em->persist($personRole);
        }
        $em->flush();
    }
}
