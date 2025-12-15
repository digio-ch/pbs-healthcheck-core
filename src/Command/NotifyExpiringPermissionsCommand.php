<?php

namespace App\Command;

use App\Entity\Security\Permission;
use App\Model\CommandStatistics;
use App\Repository\Security\PermissionRepository;
use App\Service\PermissionService;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyExpiringPermissionsCommand extends StatisticsCommand
{
    private const NAME = 'app:notify-expiring-permissions';

    private float $totalDuration;
    private int $preExpiringPermissionsToNotify;

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var PermissionService $permissionService */
    private PermissionService $permissionService;

    public function __construct(
        PermissionRepository $permissionRepository,
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
        $this->permissionRepository = $permissionRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::NAME);
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $output->writeln('Notifying pre-expiring permissions...');

        $permissions = $this->permissionRepository->findAllExpiringPermissionsToNotify();

        foreach ($permissions as $permission) {
            $this->notifyPreExpiry($permission);

            $permission->setPreExpiryNotified(true);
            $this->permissionRepository->save($permission);
        }

        $this->totalDuration = microtime(true) - $start;
        $this->preExpiringPermissionsToNotify = count($permissions);
        $output->writeln("Sent pre-expiry emails for $this->preExpiringPermissionsToNotify permissions");

        return 0;
    }

    private function notifyPreExpiry(Permission $permission)
    {
        $owner = $permission->getOwner();

        if (is_null($owner) || !$this->isActiveOwnerOfGroup($permission->getGroup()->getId(), $owner->getId())) {
            $this->permissionService->sendPreExpiryEmailForInvitee($permission, false);

            return;
        }

        $this->permissionService->sendPreExpiryEmailForInvitee($permission, true);
        $this->permissionService->sendPreExpiryEmailForCreator($permission);
    }

    private function isActiveOwnerOfGroup(int $groupId, int $userId): bool
    {
        $permissions = $this->permissionRepository->findActiveOwnerPermissions($groupId, $userId);

        return count($permissions) > 0;
    }


    public function getStats(): CommandStatistics
    {
        return new CommandStatistics(
            $this->totalDuration,
            "sent pre-expiry emails for $this->preExpiringPermissionsToNotify permissions",
            $this->preExpiringPermissionsToNotify
        );
    }
}
