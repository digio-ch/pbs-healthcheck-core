<?php

namespace App\Service;

use App\Entity\Midata\Person;
use App\Model\InvitationMailInput;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    private MailerInterface $mailer;

    private array $pbsRecipients;

    private string $frontendBaseURL;


    public function __construct(
        string $pbsRecipients,
        string $frontendBaseURL,
        MailerInterface $mailer
    ) {
        $this->pbsRecipients = explode(",", $pbsRecipients);
        $this->frontendBaseURL = $frontendBaseURL;
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

        $subject = 'BetaAccess: ' . $person->getNickName();

        $this->sendMailToPBSTeam($subject, $content);
    }

    public function sendMailToPBSTeam(string $subject, string $content)
    {
        $email = (new Email())
            ->from($this->getFromAddress())
            ->to(...$this->pbsRecipients)
            ->subject($subject)
            ->text($content);

        $this->mailer->send($email);
    }

    public function sendInvitationMail(string $receiver, InvitationMailInput $input)
    {
        $email = (new TemplatedEmail())
            ->from($this->getFromAddress())
            ->to($receiver)
            ->subject($input->getSubject())
            ->htmlTemplate('invitation.html.twig')
            ->context([
                'baseURL' => $this->frontendBaseURL,

                'title' => $input->getTitle(),
                'name' => $input->getName(),
                'intro' => $input->getIntroductionText(),
                'context' => $input->getContextText(),
                'link' => $input->getLink(),
                'cta' => $input->getCtaText(),
            ]);

        $this->mailer->send($email);
    }

    private function getFromAddress(): Address
    {
        return new Address('no-reply@hc-prod.cust.digio.ch', 'Digio');
    }
}
