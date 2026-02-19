<?php


namespace App\Service\DigioLogger\Messages;

use Doctrine\ORM\EntityManagerInterface;

class ExceptionLogMessage extends LogMessage
{
    /** @var EntityManagerInterface|null $em */
    private $em;

    private $exception;

    private $stackTrace;

    public function __construct(\Throwable $thrown, ?EntityManagerInterface $em = null)
    {
        parent::__construct(sprintf('%s: %s', get_class($thrown), $thrown->getMessage()), 'exception');
        $this->em = $em;

        $this->exception = get_class($thrown);
        $this->stackTrace = $this->serializeStackTrace($thrown);
    }

    /**
     * @return string
     */
    public function getException(): string
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getStackTrace(): string
    {
        return $this->stackTrace;
    }

    private function serializeStackTrace(\Throwable $thrown): string
    {
        $serialized = [
            ['location' => sprintf('%s:%d', $thrown->getFile(), $thrown->getLine())]
        ];

        foreach ($thrown->getTrace() as $elem) {
            $class = array_key_exists('class', $elem) ? $elem['class'] : '';

            if (array_key_exists('file', $elem)) {
                $location = sprintf('%s:%d', $elem['file'], $elem['line']);
                $call = sprintf('%s::%s', $class, $elem['function']);
            } else {
                $location = $elem['function'];
                $call = $class . '::{closure}';
            }

            $serialized[] = [
                'location' => $location,
                'call' => $call,
                'args' => array_key_exists('args', $elem) ? $this->serializeArguments($elem['args']) : [],
            ];
        }

        return json_encode($serialized);
    }

    private function serializeArguments(array $args): array
    {
        $serialized = [];
        foreach ($args as $arg) {
            $serialized[] = $this->serializeValue($arg);
        }

        return $serialized;
    }

    private function serializeValue($value)
    {
        switch (true) {
            case is_array($value):
                return array_map(function ($item) {
                    return $this->serializeValue($item);
                }, $value);
            case is_object($value):
                return $this->getDoctrineIdentifier($value);
            default:
                return $value;
        }
    }

    private function getDoctrineIdentifier($obj): ?string
    {
        if ($obj === null) {
            return null;
        }

        $class = get_class($obj);

        if ($this->em === null || $this->em->getMetadataFactory()->isTransient($class)) {
            return $class;
        }

        $metadata = $this->em->getClassMetadata($class);
        $identifier = $metadata->getIdentifierValues($obj);
        $serialized = [];

        foreach ($identifier as $key => $value) {
            $serialized[] = sprintf('%s: { %s }', $key, $this->serializeValue($value));
        }

        return sprintf('%s: { %s }', $class, join(', ', $serialized));
    }
}