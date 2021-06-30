<?php

namespace App\Command;

use App\Entity\EventGroup;
use App\Entity\YouthSportType;
use App\Entity\Camp;
use App\Entity\CampState;
use App\Entity\Course;
use App\Entity\Event;
use App\Entity\EventDate;
use App\Entity\EventType;
use App\Entity\EventTypeQualificationType;
use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\Person;
use App\Entity\PersonEvent;
use App\Entity\PersonEventType;
use App\Entity\PersonQualification;
use App\Entity\PersonRole;
use App\Entity\QualificationType;
use App\Entity\Role;
use App\Model\CommandStatistics;
use App\Repository\PersonRepository;
use DateTimeImmutable;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Exception;
use JsonMachine\JsonMachine;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportFromJsonCommand extends StatisticsCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PersonRepository $personRepository
     */
    private $personRepository;

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    /**
     * @var array
     */
    private $stats = [];

    /**
     * @var int
     */
    private $batchSize = 500;

    /**
     * ImportFromJson constructor.
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     */
    public function __construct(EntityManagerInterface $em, PersonRepository $personRepository, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->personRepository = $personRepository;
        $this->params = $params;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-data')
            ->setDescription('Import data from JSON files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ConnectionException|\Doctrine\DBAL\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sqlLogger = $this->em->getConnection()->getConfiguration()->getSQLLogger();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->em->getConnection()->beginTransaction();
        try {
            $output->writeln(['Start importing...']);
            $this->cleaningUpEntities($output);
            $this->importRoleTypes($output);
            $this->importGroupTypes($output);
            $this->importQualificationTypes($output);
            $this->importEventTypes($output);
            $this->importYouthSportTypes($output);
            $this->importCampStates($output);
            $this->importPersonEventTypes($output);
            $this->importGroups($output);
            $this->importCourses($output);
            $this->importCamps($output);
            $this->importPeople($output);
            $this->importParticipations($output);
            $this->importQualifications($output);
            $this->importRoles($output);

            $this->em->getConnection()->commit();

            $output->writeln(['Import passed successfully']);
            $this->em->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @throws \Doctrine\DBAL\Exception
     */
    private function cleaningUpEntities(OutputInterface $output)
    {
        $connection = $this->em->getConnection();

        $connection->executeQuery("DELETE FROM midata_event_date");

        $connection->executeQuery("DELETE FROM midata_event_group");

        $output->writeln("cleaned some entities from the db");
    }

    /**
     * @param OutputInterface $output
     */
    private function importRoleTypes(OutputInterface $output)
    {
        $start = microtime(true);
        $rolesTypes = JsonMachine::fromFile(sprintf('%s/role_types.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($rolesTypes as $roleType) {
            $role = $this->em->getRepository(Role::class)->findOneBy(['roleType' => $roleType['role_type']]);
            if (!$role) {
                $role = new Role();
            }
            $role->setLayerType($roleType['layer_type']);
            $role->setGroupType($roleType['group_type']);
            $role->setRoleType($roleType['role_type']);
            $role->setDeLabel($roleType['label_de']);
            $role->setFrLabel($roleType['label_fr']);
            $role->setItLabel($roleType['label_it']);

            $this->em->persist($role);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['role_types.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from roles_types.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importGroupTypes(OutputInterface $output)
    {
        $start = microtime(true);
        $groupTypes = JsonMachine::fromFile(sprintf('%s/group_types.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($groupTypes as $type) {
            $groupType = $this->em->getRepository(GroupType::class)->findOneBy(['groupType' => $type['group_type']]);
            if (!$groupType) {
                $groupType = new GroupType();
            }
            $groupType->setGroupType($type['group_type']);
            $groupType->setDeLabel($type['label_de']);
            $groupType->setItLabel($type['label_it']);
            $groupType->setFrLabel($type['label_fr']);

            $this->em->persist($groupType);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['group_types.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from group_types.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importQualificationTypes(OutputInterface $output)
    {
        $start = microtime(true);
        $qualificationType = JsonMachine::fromFile(
            sprintf('%s/qualification_kinds.json', $this->params->get('import_data_dir'))
        );
        $i = 0;
        foreach ($qualificationType as $type) {
            $qualificationType = $this->em->getRepository(QualificationType::class)->findOneBy([
                'id' => $type['id']
            ]);
            if (!$qualificationType) {
                $qualificationType = new QualificationType();
                $qualificationType->setId($type['id']);
                $metadata = $this->em->getClassMetaData(get_class($qualificationType));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $qualificationType->setValidity($type['validity']);
            $qualificationType->setDeLabel($type['label_de']);
            $qualificationType->setItLabel($type['label_it']);
            $qualificationType->setFrLabel($type['label_fr']);

            $this->em->persist($qualificationType);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['qualification_kinds.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from qualification_kinds.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importEventTypes(OutputInterface $output)
    {
        $start = microtime(true);
        $eventTypes = JsonMachine::fromFile(sprintf('%s/event_kinds.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($eventTypes as $type) {
            $eventType = $this->em->getRepository(EventType::class)->findOneBy(['id' => $type['id']]);
            if (!$eventType) {
                $eventType = new EventType();
                $eventType->setId($type['id']);
                $metadata = $this->em->getClassMetaData(get_class($eventType));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $eventType->setDeLabel($type['label_de']);
            $eventType->setItLabel($type['label_it']);
            $eventType->setFrLabel($type['label_fr']);

            if ($type['event_kind_qualification_kinds']) {
                // remove existing and re-import up to date EventTypeQualificationTypes since they have no given ids
                /** @var EventTypeQualificationType $existingQt */
                foreach ($eventType->getEventTypeQualificationTypes() as $existingQt) {
                    $this->em->remove($existingQt);
                }
                foreach ($type['event_kind_qualification_kinds'] as $qt) {
                    $eventTypeQualificatioType = new EventTypeQualificationType();
                    $eventTypeQualificatioType->setRole($qt['role']);
                    $eventTypeQualificatioType->setCategory($qt['category']);
                    $eventTypeQualificatioType->setEventType($eventType);

                    $qtEntity = $this->em->getRepository(QualificationType::class)->find($qt['qualification_kind_id']);
                    $eventTypeQualificatioType->setQualificationType($qtEntity);

                    $this->em->persist($eventTypeQualificatioType);
                }
            }

            $this->em->persist($eventType);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['event_kinds.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from event_kinds.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importYouthSportTypes(OutputInterface $output)
    {
        $start = microtime(true);
        $ageSportTypes = JsonMachine::fromFile(sprintf('%s/j_s_kinds.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($ageSportTypes as $type) {
            $ageSportType = $this->em->getRepository(YouthSportType::class)->findOneBy([
                'type' => $type['j_s_kind']
            ]);
            if (!$ageSportType) {
                $ageSportType = new YouthSportType();
            }
            $ageSportType->setType($type['j_s_kind']);
            $ageSportType->setDeLabel($type['label_de']);
            $ageSportType->setItLabel($type['label_it']);
            $ageSportType->setFrLabel($type['label_fr']);

            $this->em->persist($ageSportType);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['j_s_kinds.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from j_s_kinds.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importCampStates(OutputInterface $output)
    {
        $start = microtime(true);
        $campStates = JsonMachine::fromFile(sprintf('%s/camp_states.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($campStates as $state) {
            $campState = $this->em->getRepository(CampState::class)->findOneBy(['state' => $state['state']]);
            if (!$campState) {
                $campState = new CampState();
            }
            $campState->setState($state['state']);
            $campState->setDeLabel($state['label_de']);
            $campState->setItLabel($state['label_it']);
            $campState->setFrLabel($state['label_fr']);

            $this->em->persist($campState);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['camp_states.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from camp_states.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importPersonEventTypes(OutputInterface $output)
    {
        $start = microtime(true);
        $personEventTypes = JsonMachine::fromFile(
            sprintf('%s/participation_types.json', $this->params->get('import_data_dir'))
        );
        $i = 0;
        foreach ($personEventTypes as $type) {
            $personEventType = $this->em->getRepository(PersonEventType::class)->findOneBy(
                ['type' => $type['participation_type']]
            );
            if (!$personEventType) {
                $personEventType = new PersonEventType();
            }
            $personEventType->setType($type['participation_type']);
            $personEventType->setDeLabel($type['label_de']);
            $personEventType->setItLabel($type['label_it']);
            $personEventType->setFrLabel($type['label_fr']);

            $this->em->persist($personEventType);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['participation_types.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from participation_types.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function importGroups(OutputInterface $output)
    {
        $start = microtime(true);
        $groups = JsonMachine::fromFile(sprintf('%s/groups.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($groups as $gr) {
            $group = $this->em->getRepository(Group::class)->findOneBy(['id' => $gr['id']]);
            if (!$group) {
                $group = new Group();
                $group->setId($gr['id']);
                $metadata = $this->em->getClassMetaData(get_class($group));
                $metadata->setIdGenerator(new AssignedGenerator());
            }

            $group->setName($gr['name']);
            $group->setCantonId($gr['canton_id']);
            $group->setCantonName($gr['canton_name']);
            $group->setCreatedAt(new DateTimeImmutable($gr['created_at']));
            if ($gr['deleted_at']) {
                $group->setDeletedAt(new DateTimeImmutable($gr['deleted_at']));
            }

            /** @var GroupType $gt */
            $gt = $this->em->getRepository(GroupType::class)->findOneBy(['groupType' => $gr['type']]);
            $group->setGroupType($gt);

            if ($gr['parent_id'] !== null) {
                $pg = $this->em->getRepository(Group::class)->find($gr['parent_id']);
                $group->setParentGroup($pg);
            }

            $this->em->persist($group);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['groups.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from groups.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function importCourses(OutputInterface $output)
    {
        $start = microtime(true);
        $courses = JsonMachine::fromFile(sprintf('%s/courses.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($courses as $c) {
            $course = $this->em->getRepository(Course::class)->findOneBy(['id' => $c['id']]);
            if (!$course) {
                $course = new Course();
                $course->setId($c['id']);
                $metadata = $this->em->getClassMetaData(get_class($course));
                $metadata->setIdGenerator(new AssignedGenerator());
            }

            /** @var EventType $eventType */
            $eventType = $this->em->getRepository(EventType::class)->find($c['kind_id']);
            $course->setEventType($eventType);

            if (isset($c['name'])) {
                $course->setName($c['name']);
            }

            if ($c['groups']) {
                foreach ($c['groups'] as $g) {
                    $group = $this->em->getRepository(Group::class)->find($g['id']);
                    if ($group) {
                        $eventGroup = new EventGroup();
                        $eventGroup->setGroup($group);
                        $eventGroup->setEvent($course);
                        $course->addGroup($eventGroup);
                    }
                }
            }

            if ($c['dates']) {
                foreach ($c['dates'] as $date) {
                    $eventDate = new EventDate();
                    $eventDate->setEvent($course);
                    $eventDate->setStartAt(new DateTimeImmutable($date['start_at']));
                    $eventDate->setEndAt(new DateTimeImmutable($date['finish_at']));
                    $this->em->persist($eventDate);
                }
            }

            $this->em->persist($course);
            $this->em->flush();
            $i++;
        }
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['courses.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from courses.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function importCamps(OutputInterface $output)
    {
        $start = microtime(true);
        $camps = JsonMachine::fromFile(sprintf('%s/camps.json', $this->params->get('import_data_dir')));
        $i = 0;

        $groupData = $this->em->getRepository(Group::class)->findAll();
        $groups = [];

        /** @var Group $group */
        foreach ($groupData as $group) {
            $groups[$group->getId()] = $group;
        }

        foreach ($camps as $c) {
            $camp = $this->em->getRepository(Camp::class)->findOneBy(['id' => $c['id']]);
            if (!$camp) {
                $camp = new Camp();
                $camp->setId($c['id']);
                $metadata = $this->em->getClassMetaData(get_class($camp));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $camp->setState($c['state']);
            $camp->setLocation(substr($c['location'], 0, 255));

            if (isset($c['name'])) {
                $camp->setName($c['name']);
            }

            /** @var YouthSportType $ageSportType */
            $ageSportType = $this->em->getRepository(YouthSportType::class)->findOneBy(['type' => $c['j_s_kind']]);
            $camp->setYouthSportType($ageSportType);

            if ($c['dates']) {
                foreach ($c['dates'] as $date) {
                    $eventDate = new EventDate();
                    $eventDate->setEvent($camp);
                    $eventDate->setStartAt(new DateTimeImmutable($date['start_at']));
                    $eventDate->setEndAt(new DateTimeImmutable($date['finish_at']));
                    $this->em->persist($eventDate);
                }
            }

            if ($c['groups']) {
                foreach ($c['groups'] as $g) {
                    if (array_key_exists($g['id'], $groups)) {
                        $group = $groups[$g['id']];
                        $eventGroup = new EventGroup();
                        $eventGroup->setGroup($group);
                        $eventGroup->setEvent($camp);
                        $camp->addGroup($eventGroup);
                    }
                }
            }

            $this->em->persist($camp);
            if ($i % 10) {
                $this->em->flush();
            }
            $i++;
        }
        $this->em->flush();

        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['camps.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from camps.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function importPeople(OutputInterface $output)
    {
        $this->personRepository->markAllAsLeft();

        $start = microtime(true);
        $people = JsonMachine::fromFile(sprintf('%s/people.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($people as $p) {
            $person = $this->em->getRepository(Person::class)->findOneBy(['id' => $p['id']]);
            if (!$person) {
                $person = new Person();
                $person->setId($p['id']);
                $metadata = $this->em->getClassMetaData(get_class($person));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
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
            } else {
                $person->setLeavingDate(null);
            }
            $person->setTown($p['town']);

            if ($p['primary_group_id']) {
                $group = $this->em->getRepository(Group::class)->find($p['primary_group_id']);
                if ($group) {
                    $person->setGroup($group);
                }
            }

            $this->em->persist($person);
            $i++;

            if (($i % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();

        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['people.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from people.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     */
    private function importParticipations(OutputInterface $output)
    {
        $start = microtime(true);
        $participations = JsonMachine::fromFile(
            sprintf('%s/participations.json', $this->params->get('import_data_dir'))
        );
        $i = 0;
        foreach ($participations as $participation) {
            $personEvent = $this->em->getRepository(PersonEvent::class)->findOneBy(
                ['id' => $participation['id']]
            );
            if (!$personEvent) {
                $personEvent = new PersonEvent();
                $personEvent->setId($participation['id']);
                $metadata = $this->em->getClassMetaData(get_class($personEvent));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $person = $this->em->getRepository(Person::class)->find($participation['person_id']);
            $event = $this->em->getRepository(Event::class)->find($participation['event_id']);
            $personEvent->setQualified($participation['qualified']);

            if ($participation['roles']) {
                foreach ($participation['roles'] as $role) {
                    $personEventType = $this->em->getRepository(PersonEventType::class)->findOneBy(
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
                $this->em->persist($personEvent);
            }
            $i++;

            if (($i % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();

        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['participations.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from participations.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function importQualifications(OutputInterface $output)
    {
        $start = microtime(true);
        $qualifications = JsonMachine::fromFile(
            sprintf('%s/qualifications.json', $this->params->get('import_data_dir'))
        );
        $i = 0;
        foreach ($qualifications as $qualification) {
            $personQualification = $this->em->getRepository(PersonQualification::class)->findOneBy(
                ['id' => $qualification['id']]
            );
            if (!$personQualification) {
                $personQualification = new PersonQualification();
                $personQualification->setId($qualification['id']);
                $metadata = $this->em->getClassMetaData(get_class($personQualification));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $personQualification->setEventOrigin($qualification['origin']);
            $personQualification->setStartAt(new DateTimeImmutable($qualification['start_at']));
            $personQualification->setEndAt(
                $qualification['finish_at'] ? new DateTimeImmutable($qualification['finish_at']) : null
            );

            $person = $this->em->getRepository(Person::class)->find($qualification['person_id']);
            if (!$person) {
                continue;
            }

            $qualificationType = $this->em->getRepository(QualificationType::class)->find(
                $qualification['qualification_kind_id']
            );

            $personQualification->setPerson($person);

            if ($qualificationType) {
                $personQualification->setQualificationType($qualificationType);
            }

            $this->em->persist($personQualification);
            $i++;

            if (($i % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();

        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['qualifications.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from qualifications.json', $i)]);
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function importRoles(OutputInterface $output)
    {
        $start = microtime(true);
        $roles = JsonMachine::fromFile(sprintf('%s/roles.json', $this->params->get('import_data_dir')));
        $i = 0;
        foreach ($roles as $r) {
            $personRole = $this->em->getRepository(PersonRole::class)->findOneBy(['id' => $r['id']]);
            if (!$personRole) {
                $personRole = new PersonRole();
                $personRole->setId($r['id']);
                $metadata = $this->em->getClassMetaData(get_class($personRole));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $person = $this->em->getRepository(Person::class)->find($r['person_id']);
            if (!$person) {
                continue;
            }
            $personRole->setPerson($person);

            $role = $this->em->getRepository(Role::class)->getOneByRoleType($r['type']);
            if ($role) {
                $personRole->setRole($role);
            }

            $group = $this->em->getRepository(Group::class)->find($r['group_id']);
            if ($group) {
                $personRole->setGroup($group);
            }

            $personRole->setCreatedAt(new DateTimeImmutable($r['created_at']));
            if ($r['deleted_at']) {
                $personRole->setDeletedAt(new DateTimeImmutable($r['deleted_at']));
            }

            $this->em->persist($personRole);
            $i++;

            if (($i % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
        $timeElapsed = microtime(true) - $start;
        $this->stats[] = ['roles.json', $timeElapsed, $i];
        $output->writeln([sprintf('%s rows imported from roles.json', $i)]);
    }

    public function getStats(): CommandStatistics
    {
        $totalItems = 0;
        $totalDuration = 0;
        $details = '';

        foreach ($this->stats as $stat) {
            $totalDuration += $stat[1];
            $totalItems += $stat[2];
            $details .= $stat[2] . ' items imported in '
                . number_format($stat[1], 2)
                . ' seconds from ' . $stat[0] . "\n";
        }

        return new CommandStatistics($totalDuration, $details, $totalItems);
    }
}
