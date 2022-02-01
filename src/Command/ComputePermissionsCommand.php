<?php

namespace App\Command;

use App\Entity\PermissionType;
use App\Model\CommandStatistics;
use App\Repository\PermissionRepository;
use App\Repository\PersonRoleRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComputePermissionsCommand extends StatisticsCommand
{
    /** @var PersonRoleRepository $personRoleRepository */
    private PersonRoleRepository $personRoleRepository;

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    private float $totalDuration = 0;

    public function __construct(
        PersonRoleRepository $personRoleRepository,
        PermissionRepository $permissionRepository
    ) {
        parent::__construct();

        $this->personRoleRepository = $personRoleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:compute-permissions");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $output->writeln('Computing peoples default permissions...');

        $coaches = $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Abteilung',
            'Group::Kantonalverband',
            'Group::Bund',
        ], [
            'Group::Abteilung::Coach',
            'Group::Region::Coach',
            'Group::Kantonalverband::Coach',
            'Group::Bund::Coach',
            'Group::Bund::GrossanlassCoach',
        ]);

        foreach ($coaches as $coach) {
            $personId = $coach['person_id'];
            $groupId = $coach['group_id'];

            $expirationDate = (new \DateTimeImmutable())->add(new \DateInterval('P1M'));

            $permission = $this->permissionRepository->findByPersonGroupAndPermission(
                $groupId,
                $personId,
                PermissionType::VIEWER
            );
            if (!is_null($permission)) {
                $permission->setExpirationDate($expirationDate);
                $this->permissionRepository->save($permission);
                continue;
            }

            $this->permissionRepository->insertPermission(
                $groupId,
                PermissionType::VIEWER,
                $expirationDate,
                $personId,
                null
            );
        }

        $leaders = $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Abteilung',
            'Group::Kantonalverband',
            'Group::Bund',
        ], [
            'Group::Abteilung::Abteilungsleitung',
            'Group::Abteilung::AbteilungsleitungStv',
            'Group::Kantonalverband::Kantonsleitung',
        ]);

        foreach ($leaders as $leader) {
            $personId = $leader['person_id'];
            $groupId = $leader['group_id'];

            $expirationDate = (new \DateTimeImmutable())->add(new \DateInterval('P5D'));

            $permission = $this->permissionRepository->findByPersonGroupAndPermission(
                $groupId,
                $personId,
                PermissionType::OWNER
            );
            if (!is_null($permission)) {
                $permission->setExpirationDate($expirationDate);
                $this->permissionRepository->save($permission);
                continue;
            }

            $this->permissionRepository->insertPermission(
                $groupId,
                PermissionType::OWNER,
                $expirationDate,
                $personId,
                null
            );
        }

        $output->writeln('finished computing all default permissions.');
        $this->totalDuration = microtime(true) - $start;
        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->totalDuration, '');
    }
}
