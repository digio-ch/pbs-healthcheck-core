<?php

namespace App\DTO\Model\FilterRequestData;

use DateTime;

class OptionalDateRequestData extends FilterRequestData
{
    /** @var DateTime|null $date */
    private $date;

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     */
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
}
