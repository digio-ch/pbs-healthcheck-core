<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Course;
use App\Entity\EventDate;
use App\Entity\EventType;
use App\Entity\Group;
use App\Repository\CourseRepository;
use App\Repository\EventDateRepository;
use App\Repository\EventTypeRepository;
use App\Repository\PersonRepository;
use App\Service\PbsApiService;
use Doctrine\ORM\EntityManagerInterface;

class CoursesFetcher extends AbstractFetcher
{
    /**
     * @var CourseRepository
     */
    private $courseRepository;
    /**
     * @var EventTypeRepository
     */
    private $eventTypeRepository;
    /**
     * @var EventDateMapper
     */
    private $eventDateMapper;
    /**
     * @var PersonRepository
     */
    private $personRepository;
    /**
     * @var EventDateRepository
     */
    private $eventDateRepository;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, EventDateMapper $eventDateMapper, PersonRepository $personRepository, EventDateRepository $eventDateRepository) {
        parent::__construct($em, $pbsApiService);
        $this->courseRepository = $this->em->getRepository(Course::class);
        $this->eventTypeRepository = $this->em->getRepository(EventType::class);
        $this->eventDateMapper = $eventDateMapper;
        $this->personRepository = $personRepository;
        $this->eventDateRepository = $eventDateRepository;
    }

    protected function fetch(Group $syncGroup, string $accessToken): array
    {
        $groupId = $syncGroup->getMidataId();
        $startDate = date('d-m-Y', strtotime('-10 years'));
        $endDate = date('d-m-Y', strtotime('+5 years'));
        $courseData = $this->pbsApiService->getApiData('/groups/'.$groupId.'/events?type=Event::Course&start_date='.$startDate.'&end_date='.$endDate, $accessToken);
        return $this->mapJsonToCourses($courseData, $syncGroup, $accessToken);
    }

    private function mapJsonToCourses(array $json, Group $syncGroup, string $accessToken): array
    {
        $coursesJson = $json['events'] ?? [];
        $linked = $json['linked'] ?? [];

        $courses = [];
        foreach ($coursesJson as $courseJson) {
            /** @var Course $course */
            $course = $this->courseRepository->findOneBy(['midataId' => $courseJson['id'], 'syncGroup' => $syncGroup]);
            if (!$course) {
                $course = new Course();
                $course->setMidataId($courseJson['id']);
                $course->setSyncGroup($syncGroup);
            }
            $course->setName($courseJson['name']);

            /** @var EventType $eventType */
            $eventType = $this->eventTypeRepository->findOneBy(['id' => $courseJson['links']['kind']]);
            $course->setEventType($eventType);

            foreach ($courseJson['links']['dates'] ?? [] as $dateId) {
                $course->addEventDate($this->eventDateMapper->mapFromJson($this->getLinked($linked, 'event_dates', $dateId), $course, $syncGroup));
            }

            $personEventsFetcher = new PersonEventsFetcher($this->em, $this->pbsApiService, $this->personRepository, $course);
            $personEvents = $personEventsFetcher->fetch($syncGroup, $accessToken);
            foreach ($personEvents as $personEvent) {
                $course->addPerson($personEvent);
            }

            $courses[] = $course;
        }

        return $courses;
    }

    public function clean(string $groupId) {
        $this->em->createQueryBuilder()
            ->delete(EventDate::class, 'ed')
            ->where('ed.syncGroup = :sync_group_id')
            ->setParameter('sync_group_id', $groupId)
            ->getQuery()
            ->execute();

        $this->em->createQueryBuilder()
            ->delete(Course::class, 'c')
            ->where('c.syncGroup = :sync_group_id')
            ->setParameter('sync_group_id', $groupId)
            ->getQuery()
            ->execute();

        $this->em->flush();
    }
}
