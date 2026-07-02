<?php

namespace App\Service\Logger;

use App\Service\Logger\Messages\ExceptionLogMessage;
use App\Service\Logger\Messages\LogMessage;
use Gelf\Message;
use Monolog\Logger;
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
    private ObjectNormalizer $normalizer;

    private Serializer $serializer;

    /** @var Logger $logger */
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('app');

        $this->normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $this->serializer = new Serializer([$this->normalizer], ['json' => new JsonEncoder()]);
    }

    public function debug(LogMessage $message)
    {
        $this->send('debug', $message);
    }

    public function info(LogMessage $message)
    {
        $this->send('info', $message);
    }

    public function warning(LogMessage $message)
    {
        $this->send('warning', $message);
    }

    public function critical(LogMessage $message)
    {
        $this->send('critical', $message);
    }

    private function send(string $level, LogMessage $message)
    {
        $msg = new Message();

        try {
            $data = $this->normalizer->normalize($message);
        } catch (ExceptionInterface $e) {
            $this->warning(new ExceptionLogMessage($e));
            return;
        }

        foreach ($data as $key => $value) {
            if ($key === 'message') {
                $msg->setShortMessage($value);
                continue;
            }

            $serialized = $value;
            if (is_array($value) || is_object($value)) {
                $serialized = $this->serializer->serialize($value, 'json');
            } elseif (!is_string($value)) {
                $serialized = strval($value);
            }

            $msg->setAdditional($key, $serialized);
        }

        $this->logger->log($level, $msg->getShortMessage(), $msg->getAllAdditionals());
    }
}
