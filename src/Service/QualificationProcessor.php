<?php

namespace App\Service;

use App\DTO\Model\Apps\Widgets\LeaderDTO;
use App\DTO\Model\Apps\Widgets\QualificationDTO;
use App\Entity\Aggregated\AggregatedLeaderOverviewQualification;
use App\Entity\Midata\QualificationType;
use App\Repository\Midata\QualificationTypeRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class QualificationProcessor
{
    private const RELEVANT_QUALIFICATION_IDS = [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 14, 22, 23, 24, 25, 26, 27];
    private const GROUP_SPECIFIC_QUALIFICATION_TYPE_IDS = [
        'Group::Biber' => 2,
        'Group::Pio' => 3,
        'Group::Pta' => 4,
        'Group::AbteilungsRover' => 5,
        'Group::Abteilung' => 1
    ];
    private const QUALIFICATION_TYPE_COLORS = [
        1 => '#65D1B5',
        2 => '#65D1B5',
        3 => '#65D1B5',
        4 => '#65D1B5',
        5 => '#65D1B5',
        7 => '#C9F381',
        8 => '#65D1B5',
        9 => '#C9F381',
        11 => '#C9F381',
        10 => '#65D1B5',
        14 => '#C9F381',
        22 => '#88f3b2',
        23 => '#9fea95',
        24 => '#9fea95',
        25 => '#52a8a1',
        26 => '#52a8a1',
        27 => '#52a8a1',
    ];

    /**
     * @var string
     */
    private $currentLocale;

    /**
     * @var QualificationTypeRepository
     */
    private $qualificationTypeRepository;

    /**
     * QualificationProcessor constructor.
     * @param RequestStack $requestStack
     * @param QualificationTypeRepository $qualificationTypeRepository
     */
    public function __construct(RequestStack $requestStack, QualificationTypeRepository $qualificationTypeRepository)
    {
        $this->currentLocale = $requestStack->getCurrentRequest()->getLocale();
        $this->qualificationTypeRepository = $qualificationTypeRepository;
    }

    /**
     * @param array|AggregatedLeaderOverviewQualification[] $leaderOverviewQualifications
     * @param string $groupName
     * @return array
     */
    public function process(array $leaderOverviewQualifications, string $groupName): array
    {
        $uniqueQualifications = $this->removeUnneeded($leaderOverviewQualifications);
        $qualifications = array_merge(
            $this->processEntryCourses($uniqueQualifications),
            $this->processAdvancedCourses($uniqueQualifications),
            $this->processOtherQualifications($uniqueQualifications)
        );
        $this->processGroupSpecificQualifications($groupName, $leaderOverviewQualifications, $qualifications);
        return $qualifications;
    }

    /**
     * @param array $leaderOverviewQualifications
     * @param LeaderDTO $leaderDTO
     * @return array
     */
    public function translateAndAddToLeaderDTOs(array $leaderOverviewQualifications, LeaderDTO $leaderDTO)
    {
        $result = [];
        /** @var AggregatedLeaderOverviewQualification $qualification */
        foreach ($leaderOverviewQualifications as $qualification) {
            $qualificationDTO = new QualificationDTO();
            $qualificationDTO->setState($qualification->getState());
            $qualificationDTO->setEventOrigin($qualification->getEventOrigin());
            $qualificationDTO->setExpiresAt(
                $qualification->getExpiresAt() instanceof \DateTimeImmutable ?
                    $qualification->getExpiresAt()->format('Y-m-d')
                    : 'No expiration date'
            );
            if (!$qualification->getQualificationType()) {
                $leaderDTO->addQualification($qualificationDTO);
                continue;
            }
            $id = $qualification->getQualificationType()->getId();
            $qualificationDTO->setFullName(
                $this->qualificationTypeRepository->findTranslation($this->currentLocale, $id)[0]
            );
            $qualificationDTO->setColor(
                $qualification->getState() === 'expired' ? '#d3d3d3' : self::QUALIFICATION_TYPE_COLORS[$id]
            );
            if (isset(QualificationType::$qualificationTypesShortcuts[$id])) {
                $qualificationDTO->setShortName(QualificationType::$qualificationTypesShortcuts[$id]);
            }
            $leaderDTO->addQualification($qualificationDTO);
        }
        return $result;
    }

    /**
     * @param array $leaderOverviewQualifications
     * @return array
     */
    private function processEntryCourses(array $leaderOverviewQualifications): array
    {
        $result = [];
        $addedJs = $this->addQualificationConditionally(23, $result, $leaderOverviewQualifications);
        $addedKs = $this->addQualificationConditionally(24, $result, $leaderOverviewQualifications);
        if ($addedJs || $addedKs) {
            return $result;
        }
        $this->addQualificationConditionally(14, $result, $leaderOverviewQualifications);
        if ($this->addQualificationConditionally(7, $result, $leaderOverviewQualifications)) {
            return $result;
        }
        if ($this->addQualificationConditionally(11, $result, $leaderOverviewQualifications)) {
            return $result;
        }
        $this->addQualificationConditionally(9, $result, $leaderOverviewQualifications);
        return $result;
    }

    /**
     * @param array $leaderOverviewQualifications
     * @return array
     */
    private function processAdvancedCourses(array $leaderOverviewQualifications): array
    {
        $result = [];
        if ($this->addQualificationConditionally(8, $result, $leaderOverviewQualifications)) {
            return $result;
        }
        $this->addQualificationConditionally(10, $result, $leaderOverviewQualifications);
        return $result;
    }

    /**
     * @param array $leaderOverviewQualifications
     * @return array
     */
    private function processOtherQualifications(array $leaderOverviewQualifications): array
    {
        $result = [];
        $otherIds = [22, 25, 26, 27];
        foreach ($otherIds as $id) {
            $this->addQualificationConditionally($id, $result, $leaderOverviewQualifications);
        }
        return $result;
    }

    /**
     * @param string $groupName
     * @param array $leaderOverviewQualifications
     * @param array $qualifications
     */
    private function processGroupSpecificQualifications(
        string $groupName,
        array $leaderOverviewQualifications,
        array &$qualifications
    ) {
        if (!array_key_exists($groupName, self::GROUP_SPECIFIC_QUALIFICATION_TYPE_IDS)) {
            return;
        }
        $this->addQualificationConditionally(
            self::GROUP_SPECIFIC_QUALIFICATION_TYPE_IDS[$groupName],
            $qualifications,
            $leaderOverviewQualifications
        );
    }

    /**
     * Remove qualifications which do not need to be shown in front-end
     * @param array $qualifications
     * @return array
     */
    private function removeUnneeded(array $qualifications): array
    {
        /** @var AggregatedLeaderOverviewQualification[] $unique */
        $temp = [];
        // Sort Qualifications by expiration date descending, and null goes first.
        // This is so the newest qualification is sent to the frontend and old ones get filtered out.
        usort($qualifications, function (AggregatedLeaderOverviewQualification $a, AggregatedLeaderOverviewQualification $b) {
            if(is_null($a->getExpiresAt())) return -1;
            if(is_null($b->getExpiresAt())) return 1;
            if ($a->getExpiresAt()->getTimestamp() === $b->getExpiresAt()->getTimestamp()) return 0;
            return ($a->getExpiresAt()->getTimestamp() < $b->getExpiresAt()->getTimestamp()) ? 1 : -1;
        });
        /** @var AggregatedLeaderOverviewQualification $qualification */
        foreach ($qualifications as $qualification) {
            $exists = false;
            /** @var AggregatedLeaderOverviewQualification $q */
            foreach ($temp as $q) {
                if ($q->getQualificationType()->getId() === $qualification->getQualificationType()->getId()) {
                    $exists = true;
                }
            }
            if (!$qualification->getQualificationType() || $exists) {
                continue;
            }
            if (!in_array($qualification->getQualificationType()->getId(), self::RELEVANT_QUALIFICATION_IDS)) {
                continue;
            }
            $temp[] = $qualification;
        }
        return $temp;
    }

    /**
     * Add qualification to the specified array if it has the supplied id
     * and return a boolean indicating whether it was added to the array or not
     *
     * @param int $qualificationTypeId
     * @param array $qualifications
     * @param array $relevantQualifications
     * @return bool
     */
    private function addQualificationConditionally(
        int $qualificationTypeId,
        array &$qualifications,
        array $relevantQualifications
    ): bool {
        /** @var AggregatedLeaderOverviewQualification $qualification */
        foreach ($relevantQualifications as $qualification) {
            if (!$qualification->getQualificationType()) {
                continue;
            }
            if ($qualification->getQualificationType()->getId() === $qualificationTypeId) {
                $qualifications[] = $qualification;
                return true;
            }
        }
        return false;
    }
}
