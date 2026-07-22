<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Service\Logger\Messages\ExceptionLogMessage;
use App\Service\Logger\Messages\LogMessage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Legacy logger - Wraps the monolog logger
 *
 * Should be replaced by monolog completely
 */
class AppLogger
{
    private NormalizerInterface $normalizer;

    private Serializer $serializer;

    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $this->serializer = new Serializer([$this->normalizer], ['json' => new JsonEncoder()]);
    }

    public function debug(LogMessage $message): void
    {
        $this->send('debug', $message);
    }

    public function info(LogMessage $message): void
    {
        $this->send('info', $message);
    }

    public function warning(LogMessage $message): void
    {
        $this->send('warning', $message);
    }

    public function critical(LogMessage $message): void
    {
        $this->send('critical', $message);
    }

    private function send(string $level, LogMessage $message): void
    {
        try {
            $data = $this->normalizer->normalize($message);
        } catch (ExceptionInterface $e) {
            $this->warning(new ExceptionLogMessage($e));
            return;
        }

        $msg = '';
        $context = [];

        foreach ($data as $key => $value) {
            if ($key === 'message') {
                $msg = $value;
                continue;
            }

            $serialized = $value;
            if (is_array($value) || is_object($value)) {
                $serialized = $this->serializer->serialize($value, 'json');
            } elseif (!is_string($value)) {
                $serialized = strval($value);
            }

            $context[$key] = $serialized;
        }

        $this->logger->log($level, $msg, $context);
    }
}
