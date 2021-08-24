<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Group;
use App\Entity\Person;
use App\Repository\GroupRepository;
use App\Repository\PersonRepository;
use App\Service\PbsApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

class PeopleFetcher
{
    /**
     * @var int
     */
    private $batchSize = 100;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PersonRepository
     */
    private $personRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var PbsApiService
     */
    private $pbsApiService;
    /**
     * @var PersonRoleMapper
     */
    private $roleMapper;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, PersonRoleMapper $roleMapper) {
        $this->em = $em;
        $this->personRepository = $this->em->getRepository(Person::class);
        $this->groupRepository = $this->em->getRepository(Group::class);
        $this->pbsApiService = $pbsApiService;
        $this->roleMapper = $roleMapper;
    }

    public function fetchAndPersistPeople(string $groupId, string $accessToken)
    {
        $i = 0;
        foreach ($this->fetchPeople($groupId, $accessToken) as $person) {
            $this->em->persist($person);
            $i++;

            if (($i % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
    }

    private function fetchPeople(string $groupId, string $accessToken): array
    {
        $peopleData = $this->pbsApiService->getApiData('/groups/'.$groupId.'/people?filters[role][kind]=with_deleted&range=layer', $accessToken);
        return $this->mapJsonToPeople($peopleData);
    }

    private function mapJsonToPeople(array $json): array
    {
        $peopleJson = $json['people'] ?? [];
        $linked = $json['linked'] ?? [];

        $people = [];
        foreach ($peopleJson as $personJson) {
            $person = $this->personRepository->findOneBy(['id' => $personJson['id']]);
            if (!$person) {
                $person = new Person();
                $person->setId($personJson['id']);
                $metadata = $this->em->getClassMetaData(get_class($person));
                $metadata->setIdGenerator(new AssignedGenerator());
            }
            $person->setNickname($personJson['nickname']);
            $person->setGender($personJson['gender']);
            $person->setAddress($personJson['address']);
            $person->setCountry($personJson['country']);
            $person->setZip(intval($personJson['zip_code']));
            if ($personJson['birthday']) {
                $person->setBirthday(new DateTimeImmutable($personJson['birthday']));
            }
            $person->setPbsNumber($personJson['pbs_number']);
            if ($personJson['entry_date']) {
                $person->setEntryDate(new DateTimeImmutable($personJson['entry_date']));
            }
            if ($personJson['leaving_date']) {
                $person->setLeavingDate(new DateTimeImmutable($personJson['leaving_date']));
            } else {
                $person->setLeavingDate(null);
            }
            $person->setTown($personJson['town']);

            if ($personJson['primary_group_id']) {
                /** @var Group $group */
                $group = $this->groupRepository->find($personJson['primary_group_id']);
                if ($group) {
                    $person->setGroup($group);
                }
            }

            foreach ($personJson['links']['roles'] ?? [] as $roleId) {
                $person->clearPersonRoles();
                $person->addPersonRole($this->roleMapper->mapFromJson($this->getLinked($linked, 'roles', $roleId)));
            }

            $people[] = $person;
        }

        return $people;
    }

    private function getLinked(array $linked, string $rel, string $id) {
        return array_filter($linked[$rel] ?? [], function($linkedEntity) use ($id) {
            return $linkedEntity['id'] === $id;
        })[0] ?? null;
    }
}
