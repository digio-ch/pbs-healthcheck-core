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
    const ANNOTATION = 'json_object';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
        // return the SQL used to create your column type. To create a portable column type, use the $platform.
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $json = json_encode($value, JSON_FORCE_OBJECT);

        if ($json === false) {
            throw ConversionException::conversionFailedSerialization(
                $value,
                'json',
                new \RuntimeException(json_last_error_msg()),
            );
        }

        return $json;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return array
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConversionException::conversionFailed($value, self::ANNOTATION);
        }

        return $decoded;
    }

    public function getName(): string
    {
        return self::ANNOTATION;
    }
}
