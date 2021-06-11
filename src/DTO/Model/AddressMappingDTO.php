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
     * @return string|null
     */
    public function getMidataAddress(): ?string
    {
        return $this->midataAddress;
    }

    /**
     * @param string|null $midataAddress
     */
    public function setMidataAddress(?string $midataAddress): void
    {
        $this->midataAddress = $midataAddress;
    }

    /**
     * @return int|null
     */
    public function getMidataZip(): ?int
    {
        return $this->midataZip;
    }

    /**
     * @param int|null $midataZip
     */
    public function setMidataZip(?int $midataZip): void
    {
        $this->midataZip = $midataZip;
    }

    /**
     * @return string|null
     */
    public function getMidataTown(): ?string
    {
        return $this->midataTown;
    }

    /**
     * @param string|null $midataTown
     */
    public function setMidataTown(?string $midataTown): void
    {
        $this->midataTown = $midataTown;
    }

    /**
     * @return string|null
     */
    public function getStreetWithoutNumber(): ?string
    {
        return $this->streetWithoutNumber;
    }

    /**
     * @param string|null $streetWithoutNumber
     */
    public function setStreetWithoutNumber(?string $streetWithoutNumber): void
    {
        $this->streetWithoutNumber = $streetWithoutNumber;
    }

    /**
     * @return string|null
     */
    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    /**
     * @param string|null $houseNumber
     */
    public function setHouseNumber(?string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * @return string|null
     */
    public function getCorrectedStreet(): ?string
    {
        return $this->correctedStreet;
    }

    /**
     * @param string|null $correctedStreet
     */
    public function setCorrectedStreet(?string $correctedStreet): void
    {
        $this->correctedStreet = $correctedStreet;
    }

    /**
     * @return string|null
     */
    public function getNormalizedStreet(): ?string
    {
        return $this->normalizedStreet;
    }

    /**
     * @param string|null $normalizedStreet
     */
    public function setNormalizedStreet(?string $normalizedStreet): void
    {
        $this->normalizedStreet = $normalizedStreet;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
