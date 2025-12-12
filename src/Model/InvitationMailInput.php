<?php

namespace App\Model;

class InvitationMailInput
{
    private string $subject;

    private string $title;

    private ?string $name = null;

    private string $introductionText;

    private string $contextText;

    private ?string $link = null;

    private ?string $ctaText = null;

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): InvitationMailInput
    {
        $this->subject = $subject;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): InvitationMailInput
    {
        $this->title = $title;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): InvitationMailInput
    {
        $this->name = $name;
        return $this;
    }

    public function getIntroductionText(): string
    {
        return $this->introductionText;
    }

    public function setIntroductionText(string $introductionText): InvitationMailInput
    {
        $this->introductionText = $introductionText;
        return $this;
    }

    public function getContextText(): string
    {
        return $this->contextText;
    }

    public function setContextText(string $contextText): InvitationMailInput
    {
        $this->contextText = $contextText;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): InvitationMailInput
    {
        $this->link = $link;
        return $this;
    }

    public function getCtaText(): ?string
    {
        return $this->ctaText;
    }

    public function setCtaText(?string $ctaText): InvitationMailInput
    {
        $this->ctaText = $ctaText;
        return $this;
    }
}
