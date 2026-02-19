<?php

namespace App\Service\DigioLogger;

use App\Service\DigioLogger\Handlers\GelfLoggerHandler;
use App\Service\DigioLogger\Messages\ExceptionLogMessage;
use App\Service\DigioLogger\Messages\LogMessage;
use Gelf\Message;
use Random\RandomException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GelfLogger
{
    /** @var ObjectNormalizer $normalizer */
    private $normalizer;

    /** @var Serializer $serializer */
    private $serializer;

    /** @var GelfLoggerHandler[] $handlers */
    private array $handlers;

    /** @var string $source */
    private $source;

    /** @var string $environment */
    private $environment;

    /** @var string $executor */
    private $executor;

    /** @var string $executionId */
    private $executionId;

    /** @var string|null $execution */
    private $execution;

    /**
     * @param GelfLoggerHandler[] $handlers
     * @param string $source
     * @param string $environment
     * @throws RandomException
     */
    public function __construct(array $handlers, string $source, string $environment)
    {
        $this->handlers = $handlers;
        $this->source = $source;
        $this->environment = $environment;

        $this->normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $this->serializer = new Serializer([$this->normalizer], ['json' => new JsonEncoder()]);

        $this->executor = (php_sapi_name() === 'cli' ? 'command' : 'request');
        $this->executionId = bin2hex(random_bytes(12));
    }

    /**
     * @return string
     */
    public function getExecutor(): string
    {
        return $this->executor;
    }

    /**
     * @return string
     */
    public function getExecutionId(): string
    {
        return $this->executionId;
    }

    /**
     * @return string|null
     */
    public function getExecution(): ?string
    {
        return $this->execution;
    }

    /**
     * @param string|null $execution
     */
    public function setExecution(?string $execution): void
    {
        $this->execution = $execution;
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
        $origin = $this->findLastStackElement();

        $msg = new Message();
        $msg->setLevel($level);
        $msg->setTimestamp(new \DateTime());
        $msg->setFile($origin['file']);
        $msg->setLine($origin['line']);
        $msg->setHost($this->source);

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

        $msg->setAdditional('env', $this->environment);
        $msg->setAdditional('executor', $this->executor);
        $msg->setAdditional('execution_id', $this->executionId);
        $msg->setAdditional('execution', $this->execution);

        foreach ($this->handlers as $handler) {
            $handler->log($msg);
        }
    }

    private function findLastStackElement(): array
    {
        $stack = debug_backtrace(0);
        $idx = 0;

        while (preg_match('/Digio\/Logging\//', $stack[$idx]['file'])) {
            $idx++;
        }

        return $stack[$idx];
    }
}
