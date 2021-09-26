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

class PeopleFetcher extends AbstractFetcher
{
    /**
     * @var PersonRepository
     */
    private $personRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var PersonRoleMapper
     */
    private $roleMapper;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService, PersonRoleMapper $roleMapper) {
        parent::__construct($em, $pbsApiService);
        $this->personRepository = $this->em->getRepository(Person::class);
        $this->groupRepository = $this->em->getRepository(Group::class);
        $this->roleMapper = $roleMapper;
    }

    protected function fetch(string $groupId, string $accessToken): array
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
            $person->setGender($personJson['gender'] ?? null);
            $person->setAddress($personJson['address']);
            $person->setCountry($personJson['country']);
            $person->setZip(intval($personJson['zip_code']));
            if ($personJson['birthday'] ?? false) {
                $person->setBirthday(new DateTimeImmutable($personJson['birthday']));
            }
            $person->setPbsNumber($personJson['pbs_number'] ?? null);
            if ($personJson['entry_date'] ?? false) {
                $person->setEntryDate(new DateTimeImmutable($personJson['entry_date']));
            }
            if ($personJson['leaving_date'] ?? false) {
                $person->setLeavingDate(new DateTimeImmutable($personJson['leaving_date']));
            } else {
                $person->setLeavingDate(null);
            }
            $person->setTown($personJson['town']);

            if ($personJson['primary_group_id'] ?? false) {
                /** @var Group $group */
                $group = $this->groupRepository->find($personJson['primary_group_id']);
                if ($group) {
                    $person->setGroup($group);
                }
            }

            foreach ($personJson['links']['roles'] ?? [] as $roleId) {
                $person->addPersonRole($this->roleMapper->mapFromJson($this->getLinked($linked, 'roles', $roleId), $person));
            }

            $people[] = $person;
        }

        return $people;
    }

    public function clean(string $groupId) {
        $this->personRepository
            ->createQueryBuilder('p')
            ->delete(Person::class, 'p')
            // TODO add layer_id during import
            //->where('p.layer_id = :layer_id')
            //->setParameter('layer_id', $groupId)
            ->getQuery()
            ->execute();
        $this->em->flush();
    }
}
