# laravel-http-client

## Httpファサードの拡張

### サービスプロバイダかなんかで追加してあげる

``` php
use Illuminate\Http\Client\PendingRequest ;

/**
 * Issue a GET request to the given URL.
 *
 * @param  string  $url
 * @param  array|string|null  $query
 * @return \Illuminate\Http\Client\Response
 */
PendingRequest::macro('get2',function(string $url, $query = null){
    $laravelData = $this->parseRequestData('GET', $url, []);
    return $this->send('GET', $url, [
        'query' => $query,
    ]);
});

```

### send部分

- GuzzleHttp\Client でリクエストしてるので置き換え
- new Illuminate\Http\Client\Response( GuzzleHttp\Psr7\Response::class )
- Psr\Http\Message\ResponseInterface::class をbindするのがよいかも

``` php
/**
 * Send the request to the given URL.
 *
 * @param  string  $method
 * @param  string  $url
 * @param  array  $options
 * @return \Illuminate\Http\Client\Response
 *
 * @throws \Exception
 */
public function send(string $method, string $url, array $options = [])
{
    $url = ltrim(rtrim($this->baseUrl, '/').'/'.ltrim($url, '/'), '/');

    if (isset($options[$this->bodyFormat])) {
        if ($this->bodyFormat === 'multipart') {
            $options[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
        }

        $options[$this->bodyFormat] = array_merge(
            $options[$this->bodyFormat], $this->pendingFiles
        );
    }

    $this->pendingFiles = [];

    return retry($this->tries ?? 1, function () use ($method, $url, $options) {
        try {
            $laravelData = $this->parseRequestData($method, $url, $options);

            // ここで送信してる

            return tap(new Response($this->buildClient()->request($method, $url, $this->mergeOptions([
                'laravel_data' => $laravelData,
                'on_stats' => function ($transferStats) {
                    $this->transferStats = $transferStats;
                },
            ], $options))), function ($response) {
                $response->cookies = $this->cookies;
                $response->transferStats = $this->transferStats;

                if ($this->tries > 1 && ! $response->successful()) {
                    $response->throw();
                }
            });
        } catch (ConnectException $e) {
            throw new ConnectionException($e->getMessage(), 0, $e);
        }
    }, $this->retryDelay ?? 100);
}

```

### Illuminate\Http\Client\Response

チェックしてないぽいからgetBody()のみ実装したらいいかも

``` php
class Response implements ArrayAccess
{
    /**
     * Create a new response instance.
     *
     * @param  \Psr\Http\Message\MessageInterface  $response
     * @return void
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

```
### GuzzleHttp\Psr7\Responseの作りかた

簡単にこれでいけそ

``` php
use GuzzleHttp\Psr7\Response ;

/**
 * @param int                                  $status  Status code
 * @param array                                $headers Response headers
 * @param string|null|resource|StreamInterface $body    Response body
 * @param string                               $version Protocol version
 * @param string|null                          $reason  Reason phrase (when empty a default will be used based on the status code)
 */
resolve(Response::class,['status' => 200,'headers'=>[],'body'=>'BodyBodyBody'])
```

### stub でリクエストに対するコールバックを設定可能

``` php
/**
 * リクエストをインターセプトし、スタブ応答を返すことができるスタブcallableを登録します。
 *
 * @param  callable  $callback
 * @return $this
 */
public function stub($callback)
{
    $this->stubCallbacks = collect($callback);

    return $this;
}

```
