<?php

namespace App\Entity\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * LegacyArrayType is a custom doctrine type that replaces the deprecated Types::ARRAY type.
 *
 * All columns using this type should eventually convert to Types::JSON or Types::SIMPLE_ARRAY.
 */
class LegacyArrayType extends Type
{
    public const NAME = 'array';

    public function getSQLDeclaration(mixed $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (empty($value)) {
            return null;
        }

        return serialize($value);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?array
    {
        if (empty($value)) {
            return null;
        }

        return unserialize($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
