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

    private string $inviteMailWhitelist;

    private array $pbsRecipients;

    private string $frontendBaseURL;

    private Address $sender;


    public function __construct(
        string $senderAddress,
        string $senderName,
        string $inviteMailWhitelist,
        string $pbsRecipients,
        string $frontendBaseURL,
        MailerInterface $mailer
    ) {
        $this->sender = new Address($senderAddress, $senderName);
        $this->inviteMailWhitelist = $inviteMailWhitelist;
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
            ->from($this->sender)
            ->to(...$this->pbsRecipients)
            ->subject($subject)
            ->text($content);

        $this->mailer->send($email);
    }

    public function sendInvitationMail(string $receiver, InvitationMailInput $input)
    {
        if (!$this->allowInviteEmailFor($receiver)) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($receiver)
            ->subject($input->getSubject())
            ->htmlTemplate('invitation.html.twig')
            ->context([
                'baseURL' => $this->frontendBaseURL,

                'title' => $input->getTitle(),
                'name' => $input->getName(),
                'sections' => $input->getSections(),
                'link' => $input->getLink(),
                'cta' => $input->getCtaText(),
            ]);

        $this->mailer->send($email);
    }

    private function allowInviteEmailFor(string $receiver): bool
    {
        if ($this->inviteMailWhitelist === "*") {
            return true;
        }

        return str_contains($this->inviteMailWhitelist, $receiver);
    }
}
