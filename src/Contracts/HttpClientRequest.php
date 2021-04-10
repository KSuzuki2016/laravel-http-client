<?php

namespace KSuzuki2016\HttpClient\Contracts;

use KSuzuki2016\HttpClient\DriverManager;
use Ksuzuki2016\HttpClient\Http\Client\HttpClientFactory;
use KSuzuki2016\HttpClient\Http\Client\HttpClientPendingRequest;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

/**
 * Class HttpClientRequest
 * @package KSuzuki2016\HttpClient\Contracts
 */
abstract class HttpClientRequest
{
    /**
     * @var \Illuminate\Http\Client\Factory|HttpClientFactory
     */
    protected $app;

    /**
     * @var array
     */
    protected $observers = [];

    /**
     * @var array
     */
    protected $macros = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * HttpClient Driver Name
     * @var null|string
     */
    protected $driver;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @param bool $debug
     * @return $this
     */
    public function debug(bool $debug = true): self
    {
        $this->debug = $debug;
        return $this;
    }

    public function observe(ResponseObserverInterface $observer): self
    {
        $this->observers[] = $observer;
        $this->initialized = false;
        return $this;
    }

    public function macro(object $macro): self
    {
        if (method_exists($macro, '__invoke')) {
            $this->macros[] = $macro;
            $this->initialized = false;
        }
        return $this;
    }

    /**
     * @param string $url
     * @param null $query
     * @return \Illuminate\Http\Client\Response|HttpClientResponse
     */
    public function get(string $url, $query = null)
    {
        return $this->getPendingRequest()->get($url, $query);
    }

    /**
     * @return \Illuminate\Http\Client\PendingRequest|HttpClientPendingRequest
     */
    protected function getPendingRequest()
    {
        return $this->factory()
            ->withHeaders($this->getHeaders())
            ->withOptions($this->getOptions());
    }

    /**
     * @return \Illuminate\Http\Client\Factory|HttpClientFactory
     */
    protected function factory()
    {
        if (!$this->initialized) {
            $this->app = app(DriverManager::class)->driver($this->driver);
            $this->registerObservers();
            $this->registerBrowserCallbacks();
            $this->initialized = true;
        }
        return $this->app;
    }

    protected function registerObservers(): void
    {
        foreach ($this->observers as $observer) {
            if (is_subclass_of($observer, ResponseObserverInterface::class)) {
                if ($observer instanceof ResponseObserverInterface) {
                    $this->app->responseObserver($observer);
                } else {
                    $this->app->responseObserver(app($observer));
                }
            }
        }
    }

    protected function registerBrowserCallbacks(): void
    {
        foreach ($this->macros as $macro) {
            $this->app->browserCallback(app($macro));
        }
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return array_merge($this->options, ['debug' => $this->debug]);
    }

    /**
     * @param string $url
     * @param array $data
     * @return \Illuminate\Http\Client\Response|HttpClientResponse
     */
    public function post(string $url, array $data = [])
    {
        return $this->getPendingRequest()->post($url, $data);
    }

}
