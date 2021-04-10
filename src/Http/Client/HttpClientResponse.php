<?php


namespace KSuzuki2016\HttpClient\Http\Client;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use KSuzuki2016\HttpClient\Contracts\ResponseCrawlerInterface;
use KSuzuki2016\HttpClient\Http\Client\Extensions\ResponseCrawler;
use KSuzuki2016\HttpClient\Http\Client\Extensions\ResponseSchema;
use KSuzuki2016\HttpClient\Http\Client\Extensions\ResponseStacks;

/**
 * Class HttpClientResponse
 *
 * @method bool successful()
 * @method bool failed()
 * @method bool serverError()
 * @method bool clientError()
 * @method int status()
 * @method bool ok()
 * @method string header($header)
 * @method array headers()
 * @method string body() レスポンスbodyを取得します
 * @method mixed json($key = null, $default = null) レスポンスのJSONを配列かスカラー値として取得します
 * @method object object() レスポンスのJSONをオブジェクトとして取得します
 * @method Collection collect($key = null) レスポンスのJSONをコレクションとして取得します
 * @method $this throw() サーバーまたはクライアントのエラーが発生した場合は、例外をスローします
 *
 * @package KSuzuki2016\HttpClient\Http\Client
 */
class HttpClientResponse extends Response
{
    use ResponseCrawler;
    use ResponseSchema;
    use ResponseStacks;

    /**
     * Get the response cookies.
     *
     * @var \GuzzleHttp\Cookie\CookieJar
     */
    public $cookies;

    /**
     * The decoded JSON response.
     *
     * @var array
     */
    protected $decoded;

    /**
     * The transfer stats for the request.
     *
     * @var \GuzzleHttp\TransferStats
     */
    public $transferStats;

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function setJson($key, $value = null): self
    {
        if (is_array($key)) {
            $this->decoded = $key;
        } else if (is_string($key) || is_int($key)) {
            $this->decoded[$key] = $value;
        }
        return $this;
    }

}
