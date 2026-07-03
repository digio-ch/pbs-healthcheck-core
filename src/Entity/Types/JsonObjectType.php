<?php

namespace App\Entity\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * JsonObjectType is a custom doctrine type that ensures that arrays are stored as object (associative arrays)
 */
class JsonObjectType extends Type
{
    public const NAME = 'json_object';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
        // return the SQL used to create your column type. To create a portable column type, use the $platform.
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        $json = json_encode($value, JSON_FORCE_OBJECT);

        if ($json === false) {
            throw new ConversionException(
              'cannot convert "' . $value . '" to db type ' . self::NAME . ": " . json_last_error_msg(),
            );
        }

        return $json;
    }

    /**
     * @param mixed $value
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConversionException('cannot convert "' . $value . '" to doctrine cust type ' . self::NAME);
        }

        return $decoded;
    }
}
