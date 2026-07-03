<?php

namespace App\DTO\Model\Apps\Overview;

class OverviewSharingDTO
{
    private bool $sharing;

    public function __construct(bool $sharing)
    {
        $this->sharing = $sharing;
    }


    public function isSharing(): bool
    {
        return $this->sharing;
    }

    public function setSharing(bool $sharing): void
    {
        $this->sharing = $sharing;
    }
}
