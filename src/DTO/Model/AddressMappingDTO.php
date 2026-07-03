<?php

namespace App\DTO\Model;

class AddressMappingDTO
{
    public const ERROR_MISSING_ATTRIBUTE = 'error code: 1 (missing attribute)';
    public const ERROR_INVALID_ADDRESS = 'error code: 2 (invalid address)';
    public const ERROR_NORMALIZING_ERROR = 'error code: 3 (no street remaining after normalization)';
    public const ERROR_NO_GEO_LOCATION = 'error code: 4 (no geo location found)';
    public const STATUS_SUCCESS = 'status code: 0 (mapped)';

    private ?string $midataAddress = null;

    private ?int $midataZip = null;

    private ?string $midataTown = null;

    private ?string $streetWithoutNumber = null;

    private ?string $houseNumber = null;

    private ?string $correctedStreet = null;

    private ?string $normalizedStreet = null;

    private ?string $code = null;

    public function getMidataAddress(): ?string
    {
        return $this->midataAddress;
    }

    public function setMidataAddress(?string $midataAddress): void
    {
        $this->midataAddress = $midataAddress;
    }

    public function getMidataZip(): ?int
    {
        return $this->midataZip;
    }

    public function setMidataZip(?int $midataZip): void
    {
        $this->midataZip = $midataZip;
    }

    public function getMidataTown(): ?string
    {
        return $this->midataTown;
    }

    public function setMidataTown(?string $midataTown): void
    {
        $this->midataTown = $midataTown;
    }

    public function getStreetWithoutNumber(): ?string
    {
        return $this->streetWithoutNumber;
    }

    public function setStreetWithoutNumber(?string $streetWithoutNumber): void
    {
        $this->streetWithoutNumber = $streetWithoutNumber;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getCorrectedStreet(): ?string
    {
        return $this->correctedStreet;
    }

    public function setCorrectedStreet(?string $correctedStreet): void
    {
        $this->correctedStreet = $correctedStreet;
    }

    public function getNormalizedStreet(): ?string
    {
        return $this->normalizedStreet;
    }

    public function setNormalizedStreet(?string $normalizedStreet): void
    {
        $this->normalizedStreet = $normalizedStreet;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
