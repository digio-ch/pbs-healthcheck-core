<?php

namespace App\Service\DataProvider;

use App\DTO\Model\LeaderDTO;
use App\DTO\Model\LeaderOverviewDTO;
use App\DTO\Model\QualificationDTO;
use App\Entity\Group;
use App\Entity\LeaderOverviewLeader;
use App\Entity\LeaderOverviewQualification;
use App\Entity\PersonQualification;
use App\Entity\WidgetLeaderOverview;
use App\Exception\ApiException;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\LeaderOverviewLeaderRepository;
use App\Repository\LeaderOverviewQualificationRepository;
use App\Repository\WidgetLeaderOverviewRepository;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\QualificationProcessor;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class LeaderOverviewDatePointDataProvider extends WidgetDataProvider
{
    /**
     * @var WidgetLeaderOverviewRepository
     */
    protected $widgetLeaderOverviewRepository;

    /**
     * @var LeaderOverviewLeaderRepository
     */
    protected $leaderOverviewLeaderRepository;

    /**
     * @var LeaderOverviewQualificationRepository
     */
    protected $leaderOverviewQualificationRepository;

    /**
     * @var QualificationProcessor
     */
    private $qualificationProcessor;

    /**
     * LeaderOverviewDatePointDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     * @param WidgetLeaderOverviewRepository $widgetLeaderOverviewRepository
     * @param LeaderOverviewLeaderRepository $leaderOverviewLeaderRepository
     * @param LeaderOverviewQualificationRepository $leaderOverviewQualificationRepository
     * @param QualificationProcessor $qualificationProcessor
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        WidgetLeaderOverviewRepository $widgetLeaderOverviewRepository,
        LeaderOverviewLeaderRepository $leaderOverviewLeaderRepository,
        LeaderOverviewQualificationRepository $leaderOverviewQualificationRepository,
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

    /**
     * @param Group $group
     * @param string $date
     * @param array $subGroupTypes
     * @param array $peopleTypes
     * @return array
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes)
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

    private function mapToLeaderOverview(string $date, int $mainGroupId, string $groupType)
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
    ) {
        $leaders = $this->leaderOverviewLeaderRepository->findAllByGroupTypeAndDate($mainGroupId, $groupType, $date);
        /** @var LeaderOverviewLeader $leader */
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

    private function sortLeaderDataByType(array &$leaderOverviewDTOs): void
    {
        usort($leaderOverviewDTOs, function (LeaderOverviewDTO $a, LeaderOverviewDTO $b) {
            $indexA = array_search($a->getName(), WidgetAggregator::$typeOrder);
            $indexB = array_search($b->getName(), WidgetAggregator::$typeOrder);
            if ($indexA === $indexB) {
                return 0;
            }
            return ($indexA > $indexB) ? 1 : -1;
        });
    }
}
