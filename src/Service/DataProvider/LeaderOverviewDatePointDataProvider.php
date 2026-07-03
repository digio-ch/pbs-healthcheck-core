<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Apps\Widgets\LeaderDTO;
use App\DTO\Model\Apps\Widgets\LeaderOverviewDTO;
use App\Entity\Aggregated\AggregatedLeaderOverviewLeader;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedLeaderOverviewLeaderRepository;
use App\Repository\Aggregated\AggregatedLeaderOverviewQualificationRepository;
use App\Repository\Aggregated\AggregatedLeaderOverviewRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Service\QualificationProcessor;
use Symfony\Contracts\Translation\TranslatorInterface;

class LeaderOverviewDatePointDataProvider extends WidgetDataProvider
{
    protected AggregatedLeaderOverviewRepository $widgetLeaderOverviewRepository;

    protected AggregatedLeaderOverviewLeaderRepository $leaderOverviewLeaderRepository;

    protected AggregatedLeaderOverviewQualificationRepository $leaderOverviewQualificationRepository;

    private QualificationProcessor $qualificationProcessor;

    /**
     * LeaderOverviewDatePointDataProvider constructor.
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        AggregatedLeaderOverviewRepository $widgetLeaderOverviewRepository,
        AggregatedLeaderOverviewLeaderRepository $leaderOverviewLeaderRepository,
        AggregatedLeaderOverviewQualificationRepository $leaderOverviewQualificationRepository,
        QualificationProcessor $qualificationProcessor
    ) {
        $this->groupRepository = $groupRepository;
        $this->widgetLeaderOverviewRepository = $widgetLeaderOverviewRepository;
        $this->leaderOverviewLeaderRepository = $leaderOverviewLeaderRepository;
        $this->leaderOverviewQualificationRepository = $leaderOverviewQualificationRepository;
        $this->qualificationProcessor = $qualificationProcessor;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes): array
    {
        $result = [];

        foreach ($subGroupTypes as $groupType) {
            $result[] = $this->mapToLeaderOverview($date, $group->getId(), $groupType);
        }

        if (in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)) {
            $result[] = $this->mapToLeaderOverview($date, $group->getId(), 'Group::Abteilung');
        }

        $this->sortLeaderDataByType($result);

        return $result;
    }

    private function mapToLeaderOverview(string $date, int $mainGroupId, string $groupType): LeaderOverviewDTO
    {
        $leaderOverviewData = $this->widgetLeaderOverviewRepository->findMaleFemaleMembersCountForGroupTypeAndDate($mainGroupId, [$groupType], $date);
        $memberTypeTranslation = 'group.labels.leaderOverview.memberType.' . ($groupType === 'Group::Abteilung' ? 'leaders' : 'members');
        $leaderTypeTranslation = 'group.labels.leaderOverview.leaderType.' . ($groupType === 'Group::Abteilung' ? 'leadersOfMainGroup' : 'leadersOfSubGroup');

        $leaderOverviewDTO = new LeaderOverviewDTO();
        $leaderOverviewDTO->setName($this->translator->trans('group.labels.leaderOverview.' . $groupType));
        $leaderOverviewDTO->setSummaryMembersType($this->translator->trans($memberTypeTranslation));
        $leaderOverviewDTO->setSummaryLeadersType($this->translator->trans($leaderTypeTranslation));
        $leaderOverviewDTO->setMCount($leaderOverviewData['m'] === null ? 0 : $leaderOverviewData['m']);
        $leaderOverviewDTO->setFCount($leaderOverviewData['f'] === null ? 0 : $leaderOverviewData['f']);
        $leaderOverviewDTO->setUCount($leaderOverviewData['u'] === null ? 0 : $leaderOverviewData['u']);
        $leaderOverviewDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$groupType]);
        $this->getLeaderData($date, $mainGroupId, $groupType, $leaderOverviewDTO);
        return $leaderOverviewDTO;
    }

    private function getLeaderData(
        string $date,
        int $mainGroupId,
        string $groupType,
        LeaderOverviewDTO $leaderOverviewDTO
    ): void {
        $leaders = $this->leaderOverviewLeaderRepository->findAllByGroupTypeAndDate($mainGroupId, $groupType, $date);
        /** @var AggregatedLeaderOverviewLeader $leader */
        foreach ($leaders as $leader) {
            $leaderDTO = new LeaderDTO();
            $leaderDTO->setName($leader->getName());
            $leaderDTO->setBirthday($leader->getBirthday() === null ? '' : $leader->getBirthday()->format('Y-m-d'));
            $leaderDTO->setGender($leader->getGender());

            $qualifications = $this->qualificationProcessor->process(
                $leader->getQualifications()->getValues(),
                $groupType
            );

            if (!$qualifications) {
                $leaderOverviewDTO->addLeader($leaderDTO);
                continue;
            }
            $this->qualificationProcessor->translateAndAddToLeaderDTOs($qualifications, $leaderDTO);
            $leaderOverviewDTO->addLeader($leaderDTO);
        }
    }

    /**
     * @param LeaderOverviewDTO[] $leaderOverviewDTOs
     */
    private function sortLeaderDataByType(array &$leaderOverviewDTOs): void
    {
        usort($leaderOverviewDTOs, function (LeaderOverviewDTO $a, LeaderOverviewDTO $b): int {
            return $this->sortByGroupTypes($a->getName(), $b->getName());
        });
    }
}
