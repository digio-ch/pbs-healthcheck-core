<?php

namespace App\DTO\Model;

class AddressMappingDTO
{
    public const ERROR_MISSING_ATTRIBUTE = 'error code: 1 (missing attribute)';
    public const ERROR_INVALID_ADDRESS = 'error code: 2 (invalid address)';
    public const ERROR_NORMALIZING_ERROR = 'error code: 3 (no street remaining after normalization)';
    public const ERROR_NO_GEO_LOCATION = 'error code: 4 (no geo location found)';
    public const STATUS_SUCCESS = 'status code: 0 (mapped)';

    /** @var string $midataAddress */
    private $midataAddress;

    /** @var int $midataZip */
    private $midataZip;

    /** @var string $midataTown */
    private $midataTown;

    /** @var string $streetWithoutNumber */
    private $streetWithoutNumber;

    /** @var string $houseNumber */
    private $houseNumber;

    /** @var string $correctedStreet */
    private $correctedStreet;

    /** @var string $normalizedStreet */
    private $normalizedStreet;

    /** @var string $code */
    private $code;

    /**
     * @return string
     */
    public function getMidataAddress(): string
    {
        return $this->midataAddress;
    }

    /**
     * @param string $midataAddress
     */
    public function setMidataAddress(string $midataAddress): void
    {
        $this->midataAddress = $midataAddress;
    }

    /**
     * @return int
     */
    public function getMidataZip(): int
    {
        return $this->midataZip;
    }

    /**
     * @param int $midataZip
     */
    public function setMidataZip(int $midataZip): void
    {
        $this->midataZip = $midataZip;
    }

    /**
     * @return string
     */
    public function getMidataTown(): string
    {
        return $this->midataTown;
    }

    /**
     * @param string $midataTown
     */
    public function setMidataTown(string $midataTown): void
    {
        $this->midataTown = $midataTown;
    }

    /**
     * @return string
     */
    public function getStreetWithoutNumber(): string
    {
        return $this->streetWithoutNumber;
    }

    /**
     * @param string $streetWithoutNumber
     */
    public function setStreetWithoutNumber(string $streetWithoutNumber): void
    {
        $this->streetWithoutNumber = $streetWithoutNumber;
    }

    /**
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    /**
     * @param string $houseNumber
     */
    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * @return string
     */
    public function getCorrectedStreet(): string
    {
        return $this->correctedStreet;
    }

    /**
     * @param string $correctedStreet
     */
    public function setCorrectedStreet(string $correctedStreet): void
    {
        $this->correctedStreet = $correctedStreet;
    }

    /**
     * @return string
     */
    public function getNormalizedStreet(): string
    {
        return $this->normalizedStreet;
    }

    /**
     * @param string $normalizedStreet
     */
    public function setNormalizedStreet(string $normalizedStreet): void
    {
        $this->normalizedStreet = $normalizedStreet;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
