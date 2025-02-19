<?php

namespace App\Service;

use App\DTO\Mapper\StatusMessageMapper;
use App\DTO\Model\StatusMessageDTO;
use App\Entity\General\StatusMessage;
use App\Exception\ApiException;
use App\Repository\General\StatusMessageRepository;

class StatusMessageService
{
    /**
     * @var StatusMessageRepository $statusRepo
     */
    private StatusMessageRepository $statusRepo;

    /**
     * @param StatusMessageRepository $statusRepo
     */
    public function __construct(StatusMessageRepository $statusRepo)
    {
        $this->statusRepo = $statusRepo;
    }


    /**
     * @param string $lang
     * @return StatusMessageDTO
     * @throws ApiException
     */
    public function getStatus(string $lang): StatusMessageDTO
    {
        $state = $this->statusRepo->findOneBy([]);

        if ($state === null || $state->getSeverity() === StatusMessage::NONE) {
            return new StatusMessageDTO(StatusMessage::NONE);
        }

        return StatusMessageMapper::mapStatusBanner($state, $lang);
    }
}