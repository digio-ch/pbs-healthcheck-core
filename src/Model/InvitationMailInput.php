<?php

namespace App\Model;

class InvitationMailInput
{
    private string $subject;

    private string $title;

    private ?string $name = null;

    /**
     * @var string[] $sections
     */
    private array $sections;

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

    /**
     * @return string[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param string[] $sections
     * @return void
     */
    public function setSections(array $sections): InvitationMailInput
    {
        $this->sections = $sections;

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
