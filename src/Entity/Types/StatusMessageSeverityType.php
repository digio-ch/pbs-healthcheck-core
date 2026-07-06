<?php

declare(strict_types=1);

namespace App\Entity\Types;

use App\Entity\General\StatusMessageSeverity;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class StatusMessageSeverityType extends Type
{
    public const NAME = 'hc_status_message_severity';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return self::NAME;
    }

    /**
     * @param string|null $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?StatusMessageSeverity
    {
        return $value ? StatusMessageSeverity::from($value) : null;
    }

    /**
     * @param StatusMessageSeverity|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
