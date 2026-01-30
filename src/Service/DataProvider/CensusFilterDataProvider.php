<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Apps\Census\CensusFilterDTO;
use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\Entity\General\PersonSettings;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\General\PersonSettingsRepository;
use App\Repository\Midata\PersonRepository;
use Doctrine\ORM\NonUniqueResultException;

class CensusFilterDataProvider
{
    private PersonSettingsRepository $personSettingsRepository;
    private PersonRepository $personRepository;

    public function __construct(
        PersonSettingsRepository $personSettingsRepository,
        PersonRepository $personRepository
    ) {
        $this->personSettingsRepository = $personSettingsRepository;
        $this->personRepository = $personRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getFilterData(Group $group, int $persinId): CensusFilterDTO
    {
        $filter = $this->personSettingsRepository->findByGroupIDAndPersonID($group->getId(), $persinId);

        return $this->mapGroupSettingsToCensusFilter($filter);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function setFilterData(Group $group, int $personId, CensusRequestData $censusRequestData): CensusFilterDTO
    {
        $person = $this->provideDBPerson($personId);
        $filter = $this->buildFilterFromRequest($group, $person, $censusRequestData);

        if ($censusRequestData->isEmpty()) {
            $this->personSettingsRepository->removeByGroupIDAndPersonID($group->getId(), $personId);
        } else {
            $filter = $this->personSettingsRepository->upsert($filter);
        }

        return $this->mapGroupSettingsToCensusFilter($filter);
    }

    private function mapGroupSettingsToCensusFilter(?PersonSettings $filter): CensusFilterDTO
    {
        $filterData = new CensusFilterDTO();
        if (is_null($filter)) {
            $filterData->setRoles([]);
            $filterData->setGroups([]);
            $filterData->setFilterFemales(true);
            $filterData->setFilterMales(true);
        } else {
            $filterData->setRoles($filter->getCensusFilterRoles() ?? []);
            $filterData->setGroups($filter->getCensusFilterGroups() ?? []);
            $filterData->setFilterFemales(is_null($filter->getCensusFilterFemales()) ? true : $filter->getCensusFilterFemales());
            $filterData->setFilterMales(is_null($filter->getCensusFilterMales()) ? true : $filter->getCensusFilterMales());
        }

        return $filterData;
    }

    private function buildFilterFromRequest(Group $group, Person $person, CensusRequestData $censusRequestData): PersonSettings
    {
        $filter = new PersonSettings();
        $filter->setPerson($person);
        $filter->setGroup($group);
        $filter->setCensusFilterRoles($censusRequestData->getRoles());
        $filter->setCensusFilterGroups($censusRequestData->getGroups());
        $filter->setCensusFilterMales($censusRequestData->getFilterMales());
        $filter->setCensusFilterFemales($censusRequestData->getFilterFemales());

        return $filter;
    }

    private function provideDBPerson(int $id): ?Person
    {
        /** @var Person $person */
        $person = $this->personRepository->findOneBy(['id' => $id]);

        return $person;
    }
}
