<?php

namespace App\Command;

use App\Entity\security\Permission;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InviteCommand extends Command
{
    private const NAME = 'app:invite';

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    /** @var ValidatorInterface $validator */
    private ValidatorInterface $validator;

    /**
     * InviteCommand constructor.
     * @param GroupRepository $groupRepository
     * @param PermissionRepository $permissionRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        GroupRepository $groupRepository,
        PermissionRepository $permissionRepository,
        ValidatorInterface $validator
    ) {
        $this->groupRepository = $groupRepository;
        $this->permissionRepository = $permissionRepository;
        $this->validator = $validator;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName(self::NAME)
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the user to invite')
            ->addArgument('group_id', InputArgument::REQUIRED, 'Access will be allowed to the supplied group')
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, '# of days the invite is valid for, default is 30', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $groupId = $input->getArgument('group_id');
        $days =  intval($input->getOption('days'));

        $result = $this->groupRepository->findParentGroupById($groupId);
        if (!$result) {
            $io->error("Group with id $groupId not found.");
            return Command::SUCCESS;
        }
        $group = $result[0];

        $errors = $this->validator->validate($email, new Email());
        if (count($errors) > 0) {
            $io->error("$email is not a valid email address.");
            return Command::SUCCESS;
        }

        $result = $this->permissionRepository->findAllByGroupIdAndEmail($email, $groupId);
        if (count($result) > 0) {
            $io->warning("Invite already exists.");
            return Command::SUCCESS;
        }

        if ($days < 1 && $days > 30) {
            $io->error("Invite must be valid between 1 and 30 days");
            return Command::SUCCESS;
        }
        $expirationDate = new \DateTime();
        $expirationDate->modify("+ $days days");

        $invite = new Permission();
        $invite->setEmail($email);
        $invite->setGroup($group);
        $invite->setExpirationDate($expirationDate);

        $this->permissionRepository->save($invite);
        $io->success("Invite created successfully");

        return Command::SUCCESS;
    }
}
