<?php


namespace KSuzuki2016\HttpClient\Logging;


use Closure;
use Illuminate\Http\Client\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Log\LoggerInterface;

/**
 * Class HttpClientLogger
 * @package KSuzuki2016\HttpClient\Logging
 */
class HttpClientLogger
{
    const LOG_CHANNEL = 'http-client-log';
    /**
     * @var LoggerInterface|Logger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = Log::channel(static::LOG_CHANNEL);
    }

    /**
     * @param $method
     * @param $url
     * @param $parameters
     * @return Closure
     */
    public function logging($method, $url, $parameters): callable
    {
        return function (Response $response) use ($method, $url, $parameters) {
            $context = ['method' => $method, 'code' => $response->status(), 'url' => $url, 'data' => $parameters];
            if ($response->failed()) {
                $this->logger->error('request failed', $context);
            }
            if ($response->ok()) {
                $this->logger->debug('request ok', $context);
            }
        };
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogLevel(int $level): void
    {
        foreach ($this->getMonologLogger()->getHandlers() as $handler) {
            if ($handler instanceof AbstractProcessingHandler) {
                $handler->setLevel($level);
            }
        }
    }

    public function getMonologLogger(): \Monolog\Logger
    {
        return $this->logger->getLogger();
    }

}
