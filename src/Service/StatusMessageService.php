<?php

namespace App\Service;

use App\DTO\Mapper\StatusBannerMapper;
use App\DTO\Model\StatusBannerDTO;
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
    public function __construct(
        StatusMessageRepository $statusRepo,
        TranslatorInterface $translator,
        GelfLogger $logger
    ) {
        $this->statusRepo = $statusRepo;
        $this->translator = $translator;
        $this->logger = $logger;
    }



    /**
     * @param string $lang
     * @return StatusBannerDTO
     * @throws ApiException
     */
    public function getStatus(string $lang): StatusBannerDTO
    {
        $state = $this->statusRepo->findOneBy([]);

        if ($state === null || $state->getSeverity() === StatusMessage::NONE) {
            return new StatusBannerDTO(StatusMessage::NONE);
        }

        try {
            return StatusBannerMapper::map($state, $lang);
        } catch (\Throwable $e) {
            $message = $state->getMessage($lang);

            $this->logger->warning(new SimpleLogMessage("status banner message is invalid: $message"));
            throw new ApiException(500, $this->translator->trans('api.error.unknown'));
        }
    }
}
