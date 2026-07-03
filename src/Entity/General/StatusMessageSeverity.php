<?php

declare(strict_types=1);

namespace App\Entity\General;

enum StatusMessageSeverity: string
{
    case NONE = 'none';
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';
}
