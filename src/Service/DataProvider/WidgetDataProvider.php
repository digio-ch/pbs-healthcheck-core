<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Apps\Widgets\LeaderOverviewDTO;
use App\DTO\Model\Charts\BarChartBarDataDTO;
use App\DTO\Model\Charts\LineChartDataDTO;
use App\DTO\Model\Charts\PieChartDataDTO;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class WidgetDataProvider
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var GroupTypeRepository
     */
    protected $groupTypeRepository;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var string[] */
    public const GROUP_TYPE_COLORS = [
        'Group::Biber' => '#EEE09F',
        'Group::Woelfe' => '#3BB5DC',
        'Group::Pfadi' => '#9A7A54',
        'Group::Pio' => '#DD1F19',
        'Group::AbteilungsRover' => '#1DA650',
        'Group::Pta' => '#d9b826',
        'Group::Abteilung' => '#929292',
        'leaders' => '#929292'
    ];

    /** @var string[] */
    public const RELEVANT_SUB_GROUP_TYPES = [
        'Group::Biber',
        'Group::Woelfe',
        'Group::Pfadi',
        'Group::Pio',
        'Group::AbteilungsRover',
        'Group::Pta'
    ];

    /** @var string */
    public const PEOPLE_TYPE_LEADERS = 'leaders';
    /** @var string */
    public const PEOPLE_TYPE_MEMBERS = 'members';

    /**
     * WidgetDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupTypeRepository = $groupTypeRepository;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    protected function getLeadersColor(): string
    {
        return self::GROUP_TYPE_COLORS['leaders'];
    }

    /**
     * @param int $parentGroupId
     * @param array $subGroupTypes
     * @return array|int[]
     * @throws DBALException
     */
    protected function getSubGroupIds(
        int $parentGroupId,
        array $subGroupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ): array {
        $subGroups = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($parentGroupId, $subGroupTypes);
        if (!$subGroups) {
            throw new NotFoundHttpException('No subgroups for group with id ' . $parentGroupId . ' found');
        }
        $ids = [];
        foreach ($subGroups as $subGroup) {
            $ids[] = $subGroup['id'];
        }
        return $ids;
    }

    /**
     * @param int $parentGroupId
     * @param array|string[] $subGroupTypes
     * @return array
     * @throws DBALException
     */
    protected function getSubGroupsByType(
        int $parentGroupId,
        array $subGroupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ) {
        $subGroups = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($parentGroupId, $subGroupTypes);
        if (!$subGroups) {
            throw new NotFoundHttpException('No subgroups for group with id ' . $parentGroupId . ' found');
        }

        $groupsByType = [];
        foreach ($subGroups as $subGroup) {
            if (!array_key_exists($subGroup['group_type_id'], $groupsByType)) {
                $groupsByType[$subGroup['group_type_id']] = [
                    'group_type' => $subGroup['group_type'],
                    'groups' => []
                ];
            }
            array_push($groupsByType[$subGroup['group_type_id']]['groups'], $subGroup['id']);
        }
        return $groupsByType;
    }

    /**
     * @param BarChartBarDataDTO[]|LineChartDataDTO[]|PieChartDataDTO[]|LeaderOverviewDTO[] $items
     * @param bool $leadersOnly
     */
    protected function translateGroupNames(array $items, bool $leadersOnly = false)
    {
        foreach ($items as $item) {
            $item->setName($this->translateSingleName($item->getName(), $leadersOnly));
        }
    }

    /**
     * @param string $groupType
     * @param bool $leadersOnly
     * @return string
     */
    protected function translateSingleName(string $groupType, bool $leadersOnly = false): string
    {
        if (!in_array($groupType, self::RELEVANT_SUB_GROUP_TYPES)) {
            return $this->translator->trans("group.$groupType");
        }

        $translation = 'group.labels.'
            . ($leadersOnly ? self::PEOPLE_TYPE_LEADERS : self::PEOPLE_TYPE_MEMBERS)
            . '.' . $groupType;
        return $this->translator->trans($translation);
    }
}
