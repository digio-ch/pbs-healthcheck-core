<?php

declare(strict_types=1);

namespace App\DTO\Model;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class OptionalDateDTO
{
    public function __construct(
        #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/', message: "date format has to be YYYY-mm-dd")]
        #[Assert\Date]
        private ?string $date,
    ) {
    }

    public function getDate(): ?DateTimeInterface
    {
        if (is_null($this->date)) {
            return null;
        }

        return DateTime::createFromFormat('Y-m-d', $this->date);
    }
}
