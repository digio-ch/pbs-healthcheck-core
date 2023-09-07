<?php

namespace App\EventListener;

use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\DateRangeRequestData;
use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\FilterRequestData;
use App\DTO\Model\FilterRequestData\OptionalDateRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Midata\Group;
use App\Exception\ApiException;
use App\Repository\Midata\GroupRepository;
use App\Service\DataProvider\WidgetDataProvider;
use DateTime;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class WidgetControllerListener
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * WidgetControllerListener constructor.
     * @param GroupRepository $groupRepository
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     */
    public function __construct(
        GroupRepository $groupRepository,
        TranslatorInterface $translator,
        ValidatorInterface $validator
    ) {
        $this->groupRepository = $groupRepository;
        $this->translator = $translator;
        $this->validator = $validator;
    }


    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller) || !($controller[0]) instanceof AbstractController) {
            return;
        }

        $this->bindData($controller, $event->getRequest());
    }

    private function bindData(callable $controller, Request $request)
    {
        $actionReflection = (new ReflectionClass($controller[0]))->getMethod($controller[1]);

        foreach ($actionReflection->getParameters() as $argument) {
            if (is_null($argument->getClass())) {
                continue;
            }
            if (!(is_a($argument->getClass()->getName(), FilterRequestData::class, true) || is_a($argument->getClass()->getName(), CensusRequestData::class, true))) {
                continue;
            }
            $data = $this->validateRequest($request, $argument);
            if (!$data) {
                continue;
            }
            $request->attributes->set($argument->getName(), $data);
        }
    }

    /**
     * @param Request $request
     * @param ReflectionParameter $parameter
     * @return FilterRequestData|null
     */
    private function validateRequest(Request $request, ReflectionParameter $parameter): ?FilterRequestData
    {
        $groupId = $request->get('groupId');
        $group = $this->groupRepository->findOneByIdAndType($groupId, [
            'Group::Abteilung',
            'Group::Region',
            'Group::Kantonalverband',
            'Group::Bund',
        ]);
        if (!$group) {
            $entity = $this->translator->trans('api.entity.group');
            $message = $this->translator->trans('api.error.notFound', ['entityName' => $entity]);
            throw new ApiException(Response::HTTP_NOT_FOUND, $message);
        }

        switch ($parameter->getClass()->getName()) {
            case DateAndDateRangeRequestData::class:
                return $this->validateDateAndDateRangeRequest($group, $request);
            case DateRequestData::class:
                return $this->validateDateRequest($group, $request);
            case OptionalDateRequestData::class:
                return $this->validateOptionalDateRequest($group, $request);
            case DateRangeRequestData::class:
                return $this->validateDateRangeRequest($group, $request);
            case WidgetRequestData::class:
                return $this->validateWidgetRequest($group, $request);
            case CensusRequestData::class:
                return $this->validateCensusRequest($group, $request);
        }

        return null;
    }

    private function validateDateAndDateRangeRequest(Group $group, Request $request): DateAndDateRangeRequestData
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $date = $request->get('date', null);

        $this->checkDates($from, $to, $date, true, true);
        $data = new DateAndDateRangeRequestData();
        $data->setGroup($group);
        $data->setDate($date ? DateTime::createFromFormat('Y-m-d', $date) : null);
        $data->setFrom($from ? DateTime::createFromFormat('Y-m-d', $from) : null);
        $data->setTo($to ? DateTime::createFromFormat('Y-m-d', $to) : null);

        return $data;
    }

    private function validateDateRequest(Group $group, Request $request): DateRequestData
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $date = $request->get('date', null);

        $this->checkDates($from, $to, $date, false, true);
        $data = new DateRequestData();
        $data->setGroup($group);
        $data->setDate(DateTime::createFromFormat('Y-m-d', $date));

        return $data;
    }

    private function validateOptionalDateRequest(Group $group, Request $request): OptionalDateRequestData
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $date = $request->get('date', null);

        $data = new OptionalDateRequestData();
        if (!is_null($date)) {
            $this->checkDates($from, $to, $date, false, true);
            $data->setDate(DateTime::createFromFormat('Y-m-d', $date));
        } else {
            $data->setDate(null);
        }
        $data->setGroup($group);

        return $data;
    }

    private function validateDateRangeRequest(Group $group, Request $request): DateRangeRequestData
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $date = $request->get('date', null);

        $this->checkDates($from, $to, $date, true, false);
        $data = new DateRangeRequestData();
        $data->setGroup($group);
        $data->setFrom(DateTime::createFromFormat('Y-m-d', $from));
        $data->setTo(DateTime::createFromFormat('Y-m-d', $to));

        return $data;
    }

    private function validateWidgetRequest(Group $group, Request $request): WidgetRequestData
    {
        $groupTypes = $request->get('group-types');
        $groupTypeChoice = new Choice(WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES);
        $groupTypeChoice->min = 1;
        $groupTypeChoice->max = count(WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES);
        $groupTypeChoice->multiple = true;
        $groupTypeErrors = $this->validator->validate($groupTypes, $groupTypeChoice);

        $peopleTypes = $request->get('relevant-data');
        $peopleTypesChoice = new Choice(
            [WidgetDataProvider::PEOPLE_TYPE_MEMBERS, WidgetDataProvider::PEOPLE_TYPE_LEADERS]
        );
        $peopleTypesChoice->min = 1;
        $peopleTypesChoice->max = 2;
        $peopleTypesChoice->multiple = true;
        $peopleTypesErrors = $this->validator->validate($peopleTypes, $peopleTypesChoice);

        if (count($peopleTypesErrors) > 0 || count($groupTypeErrors) > 0) {
            $message = $this->translator->trans('api.error.invalidRequest');
            throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
        }

        $data = new WidgetRequestData();
        $data->setGroup($group);
        $data->setGroupTypes($groupTypes ?? []);
        $data->setPeopleTypes($peopleTypes ?? []);

        return $data;
    }

    private function validateCensusRequest(Group $group, Request $request): CensusRequestData
    {
        $genders = $request->get('census-filter-genders');
        $groups = $request->get('census-filter-departments');
        $roles = $request->get('census-filter-roles');
        $rolesChoice = new Choice(WidgetDataProvider::CENSUS_ROLES);
        $rolesChoice->multiple = true;
        $rolesChoice->max = count(WidgetDataProvider::CENSUS_ROLES);
        $rolesErrors = $this->validator->validate($roles, $rolesChoice);

        if (count($rolesErrors) > 0) {
            $message = $this->translator->trans('api.error.invalidRequest');
            throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
        }

        $data = new CensusRequestData();
        $data->setGroup($group);
        $data->setGroups($groups);
        $data->setRoles($roles);
        if (is_array($genders)) {
            if (array_search('m', $genders)) {
                $data->setFilterMales(true);
            }
            if (array_search('f', $genders)) {
                $data->setFilterFemales(true);
            }
        }
        return $data;
    }

    /**
     * @param $from
     * @param $to
     * @param $date
     * @param bool $isRange
     * @param bool $isDate
     */
    private function checkDates($from, $to, $date, bool $isRange, bool $isDate): void
    {
        $message = $this->translator->trans('api.error.invalidRequest');
        if ($isDate && !$isRange) {
            if (!DateTime::createFromFormat('Y-m-d', $date)) {
                throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
            }
        }

        if ($isRange && !$isDate) {
            if (!DateTime::createFromFormat('Y-m-d', $from)) {
                throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
            }
            if (!DateTime::createFromFormat('Y-m-d', $to)) {
                throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
            }
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);
        $f = DateTime::createFromFormat('Y-m-d', $from);
        $t = DateTime::createFromFormat('Y-m-d', $to);
        if ($d !== false || ($f !== false && $t !== false)) {
            return;
        }
        throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
