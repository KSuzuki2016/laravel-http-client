<?php


namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk;

use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\RequestOptions;
use KSuzuki2016\HttpClient\Http\Client\Collections\BrowserCallbackCollection;
use KSuzuki2016\HttpClient\Http\Client\HttpClient;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\HttpDusk;
use Psr\Http\Message\ResponseInterface;
use function array_key_exists;
use function is_array;

/**
 * Class Client
 * @package KSuzuki2016\HttpClient\HttpClientDrivers\Dusk
 */
class Client extends HttpClient
{
    /**
     * @var null|BrowserCallbackCollection
     */
    private $browserCallbacks;

    /**
     * @var null|array
     */
    private $config;

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $options[RequestOptions::SYNCHRONOUS] = true;
        if (strtoupper($method) === 'GET') {
            return $this->requestDusk(['url' => $uri], $options);
        }
        return $this->requestAsync($method, $uri, $options)->wait();
    }

    public function setBrowserCallbacks(BrowserCallbackCollection $browserCallbacks = null): self
    {
        $this->browserCallbacks = $browserCallbacks ?? app(BrowserCallbackCollection::class);
        return $this;
    }

    /**
     * @param $request
     * @param array $options
     * @return ResponseInterface
     */
    public function requestDusk($request, array $options = []): ResponseInterface
    {
        return HttpDusk::make($request, $this->prepareDuskOptions($options), $this->browserCallbacks)->response()->wait();
    }

    private function prepareDuskOptions(array $options): array
    {
        $defaults = (array)$this->config;

        if (!empty($defaults['headers'])) {
            // Default headers are only added if they are not present.
            $defaults['_conditional'] = $defaults['headers'];
            unset($defaults['headers']);
        }

        // Special handling for headers is required as they are added as
        // conditional headers and as headers passed to a request ctor.
        if (array_key_exists('headers', $options)) {
            // Allows default headers to be unset.
            if ($options['headers'] === null) {
                $defaults['_conditional'] = [];
                unset($options['headers']);
            } elseif (!is_array($options['headers'])) {
                throw new InvalidArgumentException('headers must be an array');
            }
        }

        // Shallow merge defaults underneath options.
        /** @noinspection AdditionOperationOnArraysInspection */
        $result = $options + $defaults;

        // Remove null values.
        foreach ($result as $k => $v) {
            if ($v === null) {
                unset($result[$k]);
            }
        }
        data_fill($result, 'headers.User-Agent', $this->defaultUserAgent());

        return $result;
    }

    protected function defaultUserAgent(): string
    {
        return 'DuskHttp';
    }
}
