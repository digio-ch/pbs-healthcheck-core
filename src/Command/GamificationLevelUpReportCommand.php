<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Repository\Gamification\LevelUpLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class GamificationLevelUpReportCommand extends StatisticsCommand
{
    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private LevelUpLogRepository $levelUpLogRepository;

    private MailerInterface $mailer;

    private float $duration = 0;

    public function __construct(
        EntityManagerInterface $em,
        LevelUpLogRepository $levelUpLogRepository,
        MailerInterface $mailer
    ) {
        parent::__construct();
        $this->em = $em;
        $this->levelUpLogRepository = $levelUpLogRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setName("app:send-levelup-report");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $levelChanges = $this->levelUpLogRepository->retrieveLastMonth();
        $mailContent = "Dear PBS Team\n\nFollowing Users have increased their rank in the last month:\n\n";
        foreach ($levelChanges as $levelChange) {
            $mailContent .= '(' . $levelChange->getDate()->format("d.m H:i") . ') '
                . $levelChange->getPerson()->getNickname()
                . '#'
                . $levelChange->getPerson()->getPbsNumber()
                . ': '
                . $levelChange->getLevel()->getDeTitle()
                . "\n";
        }
        $mailContent .= "\nKind Regards\nDigio Team
--------------------------------
Digio AG | Business made digital
Birmensdorferstrasse 94
8003 Zürich

+41 44 523 40 40
www.digio.swiss";

        $email = new Email();
        $email->from(new Address('no-reply@hc-prod.cust.digio.ch', 'Digio'))
            ->to('ss@digio.ch')
            ->subject('Level Up Report')
            ->text($mailContent);
        $this->mailer->send($email);

        $this->duration = microtime(true) - $start;
        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->duration, '');
    }
}
