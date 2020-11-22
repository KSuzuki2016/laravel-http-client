<?php


namespace KSuzuki2016\HttpClient\Http\Client;


use Closure;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse as Response;
use Exception;
use KSuzuki2016\HttpClient\ResponseObserverHandler;

class HttpClientPendingRequest extends PendingRequest
{
    public function send(string $method, string $url, array $options = [])
    {
        $url = ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');

        if (isset($options[$this->bodyFormat])) {
            if ($this->bodyFormat === 'multipart') {
                $options[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
            } elseif ($this->bodyFormat === 'body') {
                $options[$this->bodyFormat] = $this->pendingBody;
            }

            if (is_array($options[$this->bodyFormat])) {
                $options[$this->bodyFormat] = array_merge(
                    $options[$this->bodyFormat], $this->pendingFiles
                );
            }
        }

        [$this->pendingBody, $this->pendingFiles] = [null, []];

        return retry($this->tries ?? 1, function () use ($method, $url, $options) {
            try {
                $laravelData = $this->parseRequestData($method, $url, $options);

                return tap(new Response($this->buildClient()->request($method, $url, $this->mergeOptions([
                    'laravel_data' => $laravelData,
                    'on_stats' => function ($transferStats) {
                        $this->transferStats = $transferStats;
                    },
                ], $options))), function ($response) {
                    $this->fireResponseObserver($response);
                    $response->cookies = $this->cookies;
                    $response->transferStats = $this->transferStats;
                    if ($this->tries > 1 && !$response->successful()) {
                        $response->throw();
                    }
                });
            } catch (ConnectException $e) {
                throw new ConnectionException($e->getMessage(), 0, $e);
            }
        }, $this->retryDelay ?? 100);
    }
    /**
     * Build the recorder handler.
     *
     * @return Closure
     */
    public function buildRecorderHandler()
    {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                $promise = $handler($this->runBeforeSendingCallbacks($request, $options), $options);

                return $promise->then(function ($response) use ($request, $options) {
                    optional($this->factory)->recordRequestResponsePair(
                        (new Request($request))->withData($options['laravel_data']),
                        new Response($response)
                    );

                    return $response;
                });
            };
        };
    }

    public function fireResponseObserver($response)
    {
        if ($this->factory instanceof HttpClientFactory) {
            return ResponseObserverHandler::make($response, $this->factory->responseObserver)->fire();
        }
        return $response;
    }
}
