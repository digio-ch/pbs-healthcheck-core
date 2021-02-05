<?php

namespace App\EventListener;

use App\Controller\Api\Widget\WidgetController;
use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\DTO\Model\WidgetControllerData\DateRangeRequestData;
use App\DTO\Model\WidgetControllerData\DateRequestData;
use App\DTO\Model\WidgetControllerData\WidgetRequestData;
use App\Exception\ApiException;
use App\Repository\GroupRepository;
use App\Service\DataProvider\WidgetDataProvider;
use DateTime;
use ReflectionClass;
use ReflectionParameter;
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
        if (!is_array($controller) || !($controller[0]) instanceof WidgetController) {
            return;
        }

        $this->bindData($controller, $event->getRequest());
    }

    private function bindData(callable $controller, Request $request)
    {
        $actionReflection = (new ReflectionClass($controller[0]))->getMethod($controller[1]);

        foreach ($actionReflection->getParameters() as $argument) {
            if (!is_a($argument->getClass()->getName(), WidgetRequestData::class, true)) {
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
     * @return WidgetRequestData|null
     */
    private function validateRequest(Request $request, ReflectionParameter $parameter): ?WidgetRequestData
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $date = $request->get('date', null);

        $groupId = $request->get('groupId');
        $group = $this->groupRepository->findOneByIdAndType($groupId, 'Group::Abteilung');
        if (!$group) {
            $entity = $this->translator->trans('api.entity.group');
            $message = $this->translator->trans('api.error.notFound', ['entityName' => $entity]);
            throw new ApiException(Response::HTTP_NOT_FOUND, $message);
        }

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

        /** @var WidgetRequestData $data */
        $data = null;
        switch ($parameter->getClass()->getName()) {
            case DateAndDateRangeRequestData::class:
                $this->checkDates($from, $to, $date, true, true);
                $data = new DateAndDateRangeRequestData();
                $data->setDate($date ? DateTime::createFromFormat('Y-m-d', $date) : null);
                $data->setFrom($from ? DateTime::createFromFormat('Y-m-d', $from) : null);
                $data->setTo($to ? DateTime::createFromFormat('Y-m-d', $to) : null);
                break;
            case DateRequestData::class:
                $this->checkDates($from, $to, $date, false, true);
                $data = new DateRequestData();
                $data->setDate(DateTime::createFromFormat('Y-m-d', $date));
                break;
            case DateRangeRequestData::class:
                $this->checkDates($from, $to, $date, true, false);
                $data = new DateRangeRequestData();
                $data->setFrom(DateTime::createFromFormat('Y-m-d', $from));
                $data->setTo(DateTime::createFromFormat('Y-m-d', $to));
                break;
            default:
                return $data;
        }

        $data->setGroup($group);
        $data->setGroupTypes($groupTypes);
        $data->setPeopleTypes($peopleTypes);
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
