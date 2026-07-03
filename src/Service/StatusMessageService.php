<?php

namespace App\Service;

use App\DTO\Mapper\StatusBannerMapper;
use App\DTO\Model\StatusBannerDTO;
use App\Entity\General\StatusMessageSeverity;
use App\Exception\ApiException;
use App\Repository\General\StatusMessageRepository;

class StatusMessageService
{
    private StatusMessageRepository $statusRepo;

    public function __construct(StatusMessageRepository $statusRepo)
    {
        $this->statusRepo = $statusRepo;
    }

    /**
     * @throws ApiException
     */
    public function getStatus(string $lang): StatusBannerDTO
    {
        $state = $this->statusRepo->findOneBy([]);

        if ($state === null || $state->getSeverity() === StatusMessageSeverity::NONE) {
            return new StatusBannerDTO(StatusMessageSeverity::NONE->value);
        }

        return StatusBannerMapper::map($state, $lang);
    }
}
