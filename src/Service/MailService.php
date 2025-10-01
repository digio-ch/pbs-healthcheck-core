<?php

namespace App\Service;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Midata\Person;
use App\Repository\Gamification\LevelUpLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    private MailerInterface $mailer;

    private string $recipient;

    public function __construct(
        string $recipient,
        MailerInterface $mailer
    ) {
        $this->recipient = $recipient;
        $this->mailer = $mailer;
    }

    public function sendBetaAccessMail(Person $person)
    {
        $content = "Dear PBS Team
        
" .  $person->getNickname() . "(" . $person->getPbsNumber() . ") has requested beta access.

Kind Regards\nDigio Team
--------------------------------
Digio AG | Business made digital
Birmensdorferstrasse 94
8003 Zürich

+41 44 523 40 40
www.digio.swiss";
        $email = new Email();
        $email->from(new Address('no-reply@hc-prod.cust.digio.ch', 'Digio'))
            ->to($this->recipient)
            ->subject('BetaAccess: ' . $person->getNickName())
            ->text($content);
        $this->mailer->send($email);
    }

    public function sendGeneralMail(string $subject, string $content)
    {
        $email = new Email();
        $email->from(new Address('no-reply@hc-prod.cust.digio.ch', 'Digio'))
            ->to($this->recipient)
            ->subject($subject)
            ->text($content);
        $this->mailer->send($email);
    }
}
