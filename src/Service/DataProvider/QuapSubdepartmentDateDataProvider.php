<?php

namespace App\Service\DataProvider;

use App\Entity\Midata\Group;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Service\Apps\Quap\QuapService;
use Symfony\Contracts\Translation\TranslatorInterface;

class QuapSubdepartmentDateDataProvider extends WidgetDataProvider
{
    /**
     * @var QuapService $quapService
     */
    private $quapService;

    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        QuapService $quapService
    ) {
        parent::__construct($groupRepository, $groupTypeRepository, $translator);

        $this->quapService = $quapService;
    }

    public function getData(Group $group, string $date): array
    {
        $today = new \DateTime();

        $date = ($today->format('Y-m-d') === $date) ? null : \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $this->quapService->getAnswersForSubDepartments($group, $date);
    }
}
