<?php

namespace App\Service;

use DateTimeInterface;
use IntlDateFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateFormatter
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * Formats a date to "2. January 2006" where the month is translated
     * @param DateTimeInterface $date
     * @return string
     */
    public function formatLong(DateTimeInterface $date): string
    {
        $formatter = new IntlDateFormatter(
            $this->translator->getLocale(),
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            null,
            null,
            'd. MMMM yyyy'
        );

        return $formatter->format($date);
    }
}
