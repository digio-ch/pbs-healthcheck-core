<?php

namespace App\Command;

use App\Entity\Security\Permission;
use App\Repository\Midata\PersonRepository;
use App\Repository\Security\PermissionRepository;
use App\Service\Security\PermissionVoter;
use Doctrine\ORM\NonUniqueResultException;
use MongoDB\Driver\Exception\CommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyExpiringUsersCommand extends Command
{
    private const NAME = 'app:notify-expiring-users';

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var PersonRepository $personRepository */
    private PersonRepository $personRepository;

    public function __construct(
        PermissionRepository $permissionRepository,
        PersonRepository $personRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->personRepository = $personRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $permissions = $this->permissionRepository->findAllExpiringPermissionsToNotify();

        foreach ($permissions as $permission) {
            if (is_null($permission->getOwner())) {
                // TODO send email to user without owner information

                $this->persistPermission($permission);

                continue;
            }

            $dbOwner = $this->personRepository->findOneBy(['id' => $permission->getOwner()->getId()]);
            if (is_null($dbOwner)) {
                throw new CommandException("no owner found in the database");
            }

            $ownerPermission = null;
            try {
                $ownerPermission = $this->permissionRepository->findOneByPersonIDAndGroupID($dbOwner->getId(), $permission->getGroup()->getId());
            } catch (NonUniqueResultException $err) {
                throw new CommandException($err->getMessage(), 0, $err);
            }

            if (is_null($ownerPermission)) {
                continue;
            }


            if ($ownerPermission->getPermissionType()->getKey() !== PermissionVoter::OWNER) {
                continue;
            }

            // TODO send email to user and owner

            $this->persistPermission($permission);
        }

        return 0;
    }

    private function persistPermission(Permission $permission): void
    {
        $permission->setPreExpiryNotified(true);
        $this->permissionRepository->save($permission);
    }
}
