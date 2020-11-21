<?php


namespace KSuzuki2016\HttpClient\Logging;


use Illuminate\Http\Client\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class HttpClientLogger
{
    protected $logger;

    const LOG_CHANNEL = 'http-client-log';

    public function __construct()
    {
        $this->logger = Log::channel(static::LOG_CHANNEL);
    }

    public function logging($method, $url, $parameters)
    {
        return function (Response $response) use ($method, $url, $parameters) {
            $context = ['method' => $method, 'code' => $response->status(), 'url' => $url, 'data' => $parameters];
            if ($response->failed()) $this->logger->error('request failed', $context);
            if ($response->ok()) $this->logger->debug('request ok', $context);
        };
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogLevel(int $level): void
    {
        if ($this->logger instanceof Logger) {
            foreach ($this->logger->getLogger()->getHandlers() as $handler) {
                $handler->setLevel($level);
            }
        }
    }

}
