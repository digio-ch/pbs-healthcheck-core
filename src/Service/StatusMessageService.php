<?php

namespace App\Service;

use App\DTO\Mapper\StatusMessageMapper;
use App\DTO\Model\StatusMessageDTO;
use App\Entity\General\StatusMessage;
use App\Exception\ApiException;
use App\Model\LogMessage\SimpleLogMessage;
use App\Repository\General\StatusMessageRepository;
use Digio\Logging\GelfLogger;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatusMessageService
{
    /**
     * @var StatusMessageRepository $statusRepo
     */
    private StatusMessageRepository $statusRepo;

    /**
     * @var TranslatorInterface translator
     */
    private TranslatorInterface $translator;

    /**
     * @var GelfLogger
     */
    private GelfLogger $logger;

    /**
     * @param StatusMessageRepository $statusRepo
     * @param TranslatorInterface $translator
     * @param GelfLogger $logger
     */
    public function __construct(StatusMessageRepository $statusRepo, TranslatorInterface $translator, GelfLogger $logger)
    {
        $this->statusRepo = $statusRepo;
        $this->translator = $translator;
        $this->logger = $logger;
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

        $dto = StatusMessageMapper::mapStatusBanner($state, $lang);

        $message = $dto->getMessage();

        $jsonFields = json_decode($message, true);

        if ($jsonFields === null || count($jsonFields) != 2 ||
           !array_key_exists("title", $jsonFields) || !is_string($jsonFields["title"]) ||
           !array_key_exists("body", $jsonFields) || !is_string($jsonFields["body"])
        ) {
           $this->logger->warning(new SimpleLogMessage("status banner message is invalid: $message"));
           throw new ApiException(500, $this->translator->trans('api.error.unknown'));
        }

        return $dto;
    }


}