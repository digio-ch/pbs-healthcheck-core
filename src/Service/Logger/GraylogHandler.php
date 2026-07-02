<?php

declare(strict_types=1);

namespace App\Service\Logger;

use Gelf\Publisher;
use Gelf\Transport\TcpTransport;
use Gelf\Transport\TransportInterface;
use Monolog\Handler\GelfHandler;
use Monolog\Level;
use Monolog\Processor\ProcessorInterface;

class GraylogHandler extends GelfHandler
{
    /**
     * @param ProcessorInterface[] $processors
     * @param string|null $host
     * @param string|null $port
     * @param string|null $clientCert
     * @param string|null $clientKey
     * @param int|Level|string $level
     * @param bool $bubble
     */
    public function __construct(
        array $processors,
        string|null $host,
        string|null $port,
        string|null $clientCert,
        string|null $clientKey,
        int|Level|string $level = Level::Debug,
        bool $bubble = true
    )
    {
        $transport = $this->getTransporter($host, $port, $clientCert, $clientKey);

        $publisher = new Publisher($transport);

        parent::__construct($publisher, $level, $bubble);

        foreach ($processors as $processor) {
            $this->pushProcessor($processor);
        }
    }

    /**
     * Creates a Ssl transporter when the config is valid. Else it returns a stud transporter
     * @param string|null $host
     * @param string|null $port
     * @param string|null $clientCert
     * @param string|null $clientKey
     * @return TransportInterface
     */
    private function getTransporter(
        string|null $host,
        string|null $port,
        string|null $clientCert,
        string|null $clientKey,
    ): TransportInterface
    {
        $config = [$host, $port, $clientCert, $clientKey];

        if (array_any($config, fn($c) => empty($c))) {
            return new NopTransporter();
        }

        $sslOptions = new ClientAuthSslOptions();
        $sslOptions->setVerifyPeer(false);
        $sslOptions->setAllowSelfSigned(true);
        $sslOptions->setClientCert($clientCert);
        $sslOptions->setClientKey($clientKey);

        $intPort = intval($port);

        return new TcpTransport($host, $intPort, $sslOptions);

    }
}