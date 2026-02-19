<?php


namespace App\Service\DigioLogger\Handlers;

use App\Service\DigioLogger\ClientAuthSslOptions;
use Gelf\Message;
use Gelf\Publisher;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\TcpTransport;

class TcpTransportHandler implements GelfLoggerHandler
{
    /** @var Publisher $publisher */
    private $publisher;

    /**
     * GelfTransportOptions constructor.
     * @param string $host
     * @param string $port
     * @param string|null $clientCert
     * @param string|null $clientKey
     * @param bool $ignoreError
     */
    public function __construct(string $host, string $port, ?string $clientCert, ?string $clientKey, bool $ignoreError)
    {
        $sslOptions = new ClientAuthSslOptions();
        $sslOptions->setVerifyPeer(false);
        $sslOptions->setAllowSelfSigned(true);
        $sslOptions->setClientCert($clientCert);
        $sslOptions->setClientKey($clientKey);

        $transport = new TcpTransport($host, $port, $sslOptions);

        if ($ignoreError) {
            $transport = new IgnoreErrorTransportWrapper($transport);
        }

         $this->publisher = new Publisher($transport);
    }

    public function log(Message $msg)
    {
        $this->publisher->publish($msg);
    }
}