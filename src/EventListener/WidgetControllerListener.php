<?php

namespace App\EventListener;

use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\DateRangeRequestData;
use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\FilterRequestData;
use App\DTO\Model\FilterRequestData\OptionalDateRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Midata\Group;
use App\Exception\ApiException;
use App\Repository\Midata\GroupRepository;
use App\Service\Apps\Overview\OverviewSharedService;
use App\Service\DataProvider\WidgetDataProvider;
use DateTime;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/*
 * TODO: Refactor
 *
 * Move validation to the endpoints and use symfony validator
 */

class WidgetControllerListener
{
    private GroupRepository $groupRepository;

    private TranslatorInterface $translator;

    private ValidatorInterface $validator;

    private OverviewSharedService $overviewSharedService;

    /**
     * WidgetControllerListener constructor.
     */
    public function __construct(
        GroupRepository $groupRepository,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        OverviewSharedService $overviewSharedService
    ) {
        $this->groupRepository = $groupRepository;
        $this->translator = $translator;
        $this->validator = $validator;
        $this->overviewSharedService = $overviewSharedService;
    }


    #[AsEventListener(event: KernelEvents::CONTROLLER)]
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (!is_array($controller) || !($controller[0]) instanceof AbstractController) {
            return;
        }

        $this->bindData($controller, $event->getRequest());
    }

    private function bindData(callable $controller, Request $request): void
    {
        $actionReflection = (new ReflectionClass($controller[0]))->getMethod($controller[1]);

        foreach ($actionReflection->getParameters() as $argument) {
            if (is_null($argument->getType())) {
                continue;
            }
            if (!is_a($argument->getType()->getName(), FilterRequestData::class, true) && !is_a($argument->getType()->getName(), CensusRequestData::class, true)) {
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
     * @return DateAndDateRangeRequestData|DateRequestData|OptionalDateRequestData|DateRangeRequestData|WidgetRequestData|WidgetOfDepartmentRequestData|CensusRequestData|null
     */
    private function validateRequest(Request $request, ReflectionParameter $parameter): DateAndDateRangeRequestData|DateRequestData|OptionalDateRequestData|DateRangeRequestData|WidgetRequestData|WidgetOfDepartmentRequestData|CensusRequestData|null
    {
        $group = $this->extractGroup($request, 'groupId');

        switch ($parameter->getType()->getName()) {
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
            case WidgetOfDepartmentRequestData::class:
                return $this->validateWidgetOfDepartmentRequest($group, $request);
            case CensusRequestData::class:
                return $this->validateCensusRequest($group, $request);
        }

        return null;
    }

    private function validateDateAndDateRangeRequest(Group $group, Request $request): DateAndDateRangeRequestData
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $date = $request->query->get('date');

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
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $date = $request->query->get('date');

        $this->checkDates($from, $to, $date, false, true);
        $data = new DateRequestData();
        $data->setGroup($group);
        $data->setDate(DateTime::createFromFormat('Y-m-d', $date));

        return $data;
    }

    private function validateOptionalDateRequest(Group $group, Request $request): OptionalDateRequestData
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $date = $request->query->get('date');

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
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $date = $request->query->get('date');

        $this->checkDates($from, $to, $date, true, false);
        $data = new DateRangeRequestData();
        $data->setGroup($group);
        $data->setFrom(DateTime::createFromFormat('Y-m-d', $from));
        $data->setTo(DateTime::createFromFormat('Y-m-d', $to));

        return $data;
    }

    private function validateWidgetRequest(Group $group, Request $request): WidgetRequestData
    {
        $groupTypes = $request->query->all('group-types');
        $groupTypeChoice = new Choice(
            choices: WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES,
            multiple: true,
            min: 1,
            max: count(WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES),
        );
        $groupTypeErrors = $this->validator->validate($groupTypes, $groupTypeChoice);

        $peopleTypes = $request->query->all('relevant-data');
        $peopleTypesChoice = new Choice(
            choices: [WidgetDataProvider::PEOPLE_TYPE_MEMBERS, WidgetDataProvider::PEOPLE_TYPE_LEADERS],
            multiple: true,
            min: 1,
            max: 2,
        );
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

    private function validateWidgetOfDepartmentRequest(Group $group, Request $request): WidgetOfDepartmentRequestData
    {
        $department = $this->extractGroup($request, 'departmentId');

        if (!$this->overviewSharedService->validateOverviewAccess($group, $department)) {
            throw new ApiException(400, "Department has to be shared and a child of the parent group");
        }

        $widgetData = $this->validateWidgetRequest($group, $request);

        $data = new WidgetOfDepartmentRequestData();
        $data->setDepartment($department);
        $data->setGroup($widgetData->getGroup());
        $data->setGroupTypes($widgetData->getGroupTypes());
        $data->setPeopleTypes($widgetData->getPeopleTypes());

        return $data;
    }

    private function validateCensusRequest(Group $group, Request $request): CensusRequestData
    {
        $m = $request->query->getBoolean('census-filter-males', true);
        $f = $request->query->getBoolean('census-filter-females', true);
        $groups = $request->query->all('census-filter-departments');
        $roles = $request->query->all('census-filter-roles');
        $rolesChoice = new Choice(
            choices:WidgetDataProvider::CENSUS_ROLES,
            multiple: true,
            max: count(WidgetDataProvider::CENSUS_ROLES),
        );
        $rolesErrors = $this->validator->validate($roles, $rolesChoice);

        if (count($rolesErrors) > 0) {
            $message = $this->translator->trans('api.error.invalidRequest');
            throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
        }

        $data = new CensusRequestData();
        $data->setGroup($group);
        $data->setGroups($groups);
        $data->setRoles($roles);
        $data->setFilterMales($m);
        $data->setFilterFemales($f);

        return $data;
    }

    private function checkDates(?string $from,?string $to,?string $date, bool $isRange, bool $isDate): void
    {
        if ($isDate && $this->isValidDate($date)) {
            return;
        }

        if ($isRange && $this->isValidDate($from) && $this->isValidDate($to)) {
            return;
        }

        $message = $this->translator->trans('api.error.invalidRequest');
        throw new ApiException(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    private function isValidDate(?string $date): bool
    {
        return !is_null($date) && DateTime::createFromFormat('Y-m-d', $date);
    }

    /**
     * @return float|int|mixed|string
     */
    public function extractGroup(Request $request, string $key)
    {
        $groupId = $request->attributes->get($key);
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
        return $group;
    }
}
