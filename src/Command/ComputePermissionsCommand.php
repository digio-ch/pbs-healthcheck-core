<?php

namespace App\Command;

use App\Entity\security\PermissionType;
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

        $this->permissionRepository->endAllOpenPermissions();

        $coaches = $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Abteilung',
            'Group::Region',
            'Group::Kantonalverband',
            'Group::Bund',
        ], [
            'Group::Abteilung::Coach',
            'Group::Region::Coach',
            'Group::Kantonalverband::Coach',
            'Group::Bund::Coach',
        ]);
        $this->assignPermissionToRoles($coaches, PermissionType::VIEWER);

        $leaders = $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Abteilung',
            'Group::Region',
            'Group::Kantonalverband',
            'Group::Bund',
        ], [
            'Group::Abteilung::Abteilungsleitung',
            'Group::Abteilung::AbteilungsleitungStv',
            'Group::Region::Regionalleitung',
            'Group::Kantonalverband::Kantonsleitung',
        ]);
        $this->assignPermissionToRoles($leaders, PermissionType::OWNER);

        $output->writeln('finished computing all default permissions.');
        $this->totalDuration = microtime(true) - $start;
        return 0;
    }

    private function assignPermissionToRoles(array $roles, int $permissionType)
    {
        foreach ($roles as $role) {
            $personId = $role['person_id'];
            $groupId = $role['group_id'];

            $permission = $this->permissionRepository->findByPersonGroupAndPermission(
                $groupId,
                $personId,
                $permissionType
            );
            if (!is_null($permission)) {
                $permission->setExpirationDate(null);
                $this->permissionRepository->save($permission);
                continue;
            }

            $this->permissionRepository->insertPermission(
                $groupId,
                $permissionType,
                null,
                $personId,
                null
            );
        }
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->totalDuration, '');
    }
}
