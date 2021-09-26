<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Course;
use App\Entity\EventType;
use App\Entity\YouthSportType;
use App\Repository\CourseRepository;
use App\Repository\EventTypeRepository;
use App\Service\PbsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

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

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, EventDateMapper $eventDateMapper) {
        parent::__construct($em, $pbsApiService);
        $this->courseRepository = $this->em->getRepository(Course::class);
        $this->eventTypeRepository = $this->em->getRepository(EventType::class);
        $this->eventDateMapper = $eventDateMapper;
    }

    protected function fetch(string $groupId, string $accessToken): array
    {
        $startDate = date('d-m-Y', strtotime('-10 years'));
        $endDate = date('d-m-Y', strtotime('+5 years'));
        $courseData = $this->pbsApiService->getApiData('/groups/'.$groupId.'/events?type=Event::Course&start_date='.$startDate.'&end_date='.$endDate, $accessToken);
        return $this->mapJsonToCourses($courseData);
    }

    private function mapJsonToCourses(array $json): array
    {
        $coursesJson = $json['events'] ?? [];
        $linked = $json['linked'] ?? [];

        $courses = [];
        foreach ($coursesJson as $courseJson) {
            /** @var Course $course */
            $course = $this->courseRepository->findOneBy(['id' => $courseJson['id']]);
            if (!$course) {
                $course = new Course();
                $course->setId($courseJson['id']);
                $metadata = $this->em->getClassMetaData(get_class($course));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $course->setName($courseJson['name']);

            /** @var EventType $eventType */
            $eventType = $this->eventTypeRepository->findOneBy(['id' => $courseJson['links']['kind']]);
            $course->setEventType($eventType);

            $course->clearEventDates();
            foreach ($courseJson['links']['dates'] ?? [] as $dateId) {
                $course->addEventDate($this->eventDateMapper->mapFromJson($this->getLinked($linked, 'event_dates', $dateId), $course));
            }

            $courses[] = $course;
        }

        return $courses;
    }
}
