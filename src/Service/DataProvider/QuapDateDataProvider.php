<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\Entity\Midata\Group;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Service\Apps\Quap\QuapService;
use Symfony\Contracts\Translation\TranslatorInterface;

class QuapDateDataProvider extends WidgetDataProvider
{
    /** @var QuapService $quapService */
    private QuapService $quapService;

    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        QuapService $quapService
    ) {
        parent::__construct($groupRepository, $groupTypeRepository, $translator);

        $this->quapService = $quapService;
    }

    public function getData(Group $group, string $date): AnswersDTO
    {
        $today = new \DateTime();

        $date = ($today->format('Y-m-d') === $date) ? null : \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $this->quapService->getAnswers($group, $date);
    }
}
