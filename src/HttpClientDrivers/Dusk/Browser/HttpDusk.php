<?php


namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser;

use ArrayAccess;
use Closure;
use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use GuzzleHttp\Utils;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Http\Client\Collections\BrowserCallbackCollection;
use KSuzuki2016\HttpClient\Http\Client\HttpClientFactory;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome\ChromeBrowser;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome\ChromeDriver;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Contracts\DuskBrowser;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Factory;

/**
 * Class HttpDusk
 * @package KSuzuki2016\HttpClient
 */
class HttpDusk
{

    /**
     * @var null
     */
    protected $request;

    /**
     * @var array|ArrayAccess|mixed
     */
    protected $headers;

    /**
     * @var array|ArrayAccess|mixed|string
     */
    protected $url;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var
     */
    protected $driver;

    /**
     * @var
     */
    protected $browser;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|BrowserCallbackCollection|mixed
     */
    protected $browserCallbacks;

    /**
     * @var array
     */
    protected $stacks = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var int
     */
    protected $status = 200;


    /**
     * HttpDusk constructor.
     * @param null $request
     * @param array $options
     * @param null|BrowserCallbackCollection $browserCallbacks
     */
    public function __construct($request = null, array $options = [], BrowserCallbackCollection $browserCallbacks = null)
    {
        $this->request = $request;
        if ($request instanceof Request) {
            $this->headers = $request->headers();
            $this->url = $request->url();
        } elseif (is_array($request)) {
            $this->headers = Arr::get($request, 'headers', []);
            $this->url = Arr::get($request, 'url');
        }
        $this->options = $options;
        $this->browserCallbacks = $browserCallbacks ?? app(BrowserCallbackCollection::class);
    }

    /**
     * @param $request
     * @param array $options
     * @param null|BrowserCallbackCollection $browserCallbacks
     * @return static
     */
    public static function make($request, array $options = [], BrowserCallbackCollection $browserCallbacks = null): self
    {
        return new static($request, $options, $browserCallbacks);
    }

    /**
     * @param $url
     */
    public function createBrowser($url): void
    {
        $chromeOptions = new ChromeOptions;
        // ,'--window-size=375,667', '--remote-debugging-port=9222'
        $mobileEmulation = Arr::only($this->options, ['deviceMetrics']);
        $chromeOptions->addArguments(['--headless', '--disable-gpu', '--lang=ja']);
        $mobileEmulation['userAgent'] = $this->userAgent();
        $chromeOptions->setExperimentalOption('mobileEmulation', $mobileEmulation);
        $driver = new ChromeDriver($chromeOptions, app('chrome-bin-path'));
        $this->browser = $this->newBrowser($driver);
        if ($url !== 'about:blank') {
            $this->browser->visit($url);
        }
    }

    protected function newBrowser(ChromeDriver $driver): ChromeBrowser
    {
        return new ChromeBrowser($driver);
    }

    /**
     * @return array
     */
    public function header(): array
    {
        return array_filter([
            'stacks' => $this->stacks,
            'errors' => $this->errors,
        ], 'filled');
    }

    public function userAgent(): string
    {
        return Arr::get($this->options, 'userAgent', Arr::get((array)$this->headers, 'User-Agent')) ?? Utils::defaultUserAgent();
    }

    /**
     * @return mixed
     */
    public function body()
    {
        return $this->browser()->getBody();
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return DuskBrowser
     */
    public function browser(): DuskBrowser
    {
        if (!($this->browser instanceof DuskBrowser)) {
            $this->createBrowser($this->url);
        }
        return $this->browser;
    }

    /**
     * @param $url
     */
    protected function visit($url): void
    {
        $this->browser->visit($url);
    }

    public function browserCallback(): void
    {
        $this->browserCallbacks->each(function (callable $callback) {
            try {
                $stack = $callback($this->browser());
                if ($stack instanceof ArrayAccess || is_array($stack)) {
                    $stack = json_encode($stack, JSON_THROW_ON_ERROR);
                } else if (!self::isJson($stack)) {
                    $stack = json_encode((array)$stack, JSON_THROW_ON_ERROR);
                }
                $this->stacks[] = $stack;
            } catch (Exception $e) {
                $this->failed($e);
            }
        });
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isJson($value): bool
    {
        if (!in_array(substr($value, 0, 1), ['{', '[']) || !in_array(substr($value, -1), ['}', ']'])) {
            return false;
        }
        json_decode($value, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function response(): \GuzzleHttp\Promise\PromiseInterface
    {
        $this->browserCallback();
        return Http::response($this->body(), $this->status(), $this->header());
    }

    public static function http_dusk(Factory $duskRequest): Closure
    {
        return static function ($request, array $options = []) use ($duskRequest) {
            /** @var Factory|HttpClientFactory $duskRequest */
            return static::make($request, $options, $duskRequest->browserCallbacks)->response();
        };
    }

    public function failed(Exception $exception): void
    {
        $screenshot = storage_path('logs/screen/' . time());
        $this->errors[] = $exception->getMessage() . ' screenshot ' . $screenshot;
        $this->browser()->screenshot($screenshot);
        $this->status = 500;
    }

    /**
     * @param Request $request
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function __invoke(Request $request)
    {
        return $this->response();
    }
}
