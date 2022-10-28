<?php

namespace App\Service\DataProvider;

use App\DTO\Model\AnswersDTO;
use App\Entity\midata\Group;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Service\QuapService;
use Symfony\Contracts\Translation\TranslatorInterface;

class QuapDateDataProvider extends WidgetDataProvider
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

    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes): AnswersDTO
    {
        $today = new \DateTime();

        $date = ($today->format('Y-m-d') === $date) ? null : \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $this->quapService->getAnswers($group, $date);
    }
}
