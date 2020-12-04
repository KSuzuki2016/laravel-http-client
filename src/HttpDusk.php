<?php


namespace KSuzuki2016\HttpClient;

use ArrayAccess;
use Closure;
use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use GuzzleHttp\Utils;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Contracts\DuskBrowser;
use KSuzuki2016\HttpClient\Drivers\ChromeBrowser;
use KSuzuki2016\HttpClient\Drivers\ChromeDriver;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\DuskFactory;

class HttpDusk
{

    protected $request;

    protected $headers;

    protected $url;

    protected $options;

    protected $driver;

    protected $browser;

    protected $browserCallbacks;

    protected $stacks = [];

    protected $errors = [];

    protected $status = 200;


    public function __construct($request = null, array $options = [], Collection $browserCallbacks = null)
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
        $this->browserCallbacks = $browserCallbacks ?? collect();
    }

    public static function make($request, array $options = [], Collection $browserCallbacks = null): self
    {
        return new static($request, $options, $browserCallbacks);
    }

    public function createBrowser($url): void
    {
        $chromeOptions = new ChromeOptions;
        // ,'--window-size=375,667'
        $mobileEmulation = Arr::only($this->options, ['deviceMetrics']);
        $chromeOptions->addArguments(['--headless', '--disable-gpu', '--lang=ja']);
        $mobileEmulation['userAgent'] = $this->userAgent();
        $chromeOptions->setExperimentalOption('mobileEmulation', $mobileEmulation);
        $driver = new ChromeDriver($chromeOptions, app('chrome-bin-path'));
        $this->browser = new ChromeBrowser($driver);
        $this->browser->visit($url);
    }

    public function header()
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

    public function body()
    {
        return $this->browser()->getBody();
    }

    public function status()
    {
        return $this->status;
    }

    public function browser(): DuskBrowser
    {
        if (!($this->browser instanceof DuskBrowser)) {
            $this->createBrowser($this->url);
        }
        return $this->browser;
    }

    public function browserCallback(): void
    {
        $this->browserCallbacks->each(function (callable $callback) {
            try {
                $stack = $callback($this->browser());
                if ($stack instanceof ArrayAccess || is_array($stack)) {
                    $stack = json_encode($stack);
                } else if (!self::isJson($stack)) {
                    $stack = json_encode((array)$stack);
                }
                $this->stacks[] = $stack;
            } catch (Exception $e) {
                $this->failed($e);
            }
        });
    }

    public static function isJson($value)
    {
        if (!in_array(substr($value, 0, 1), ['{', '[']) || !in_array(substr($value, -1), ['}', ']'])) {
            return false;
        }
        json_decode($value);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function response()
    {
        $this->browserCallback();
        return Http::response($this->body(), $this->status(), $this->header());
    }

    public static function http_dusk(DuskFactory $duskRequest): Closure
    {
        return function ($request, array $options = []) use ($duskRequest) {
            return static::make($request, $options, $duskRequest->browserCallbacks)->response();
        };
    }

    public function failed(Exception $exception)
    {
        $screenshot = storage_path('logs/screen/' . time());
        $this->errors[] = $exception->getMessage() . ' screenshot ' . $screenshot;
        $this->browser()->screenshot($screenshot);
        $this->status = 500;
    }

    public function __invoke(Request $request)
    {
        return $this->response();
    }
}
