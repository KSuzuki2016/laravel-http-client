<?php


namespace KSuzuki2016\HttpClient;

use Closure;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Http\HttpDuskFactory;
use KSuzuki2016\HttpClient\WebDriver\ChromeBrowser;
use KSuzuki2016\HttpClient\WebDriver\Driver;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Exception;
use ArrayAccess;

class HttpDusk
{
    protected $request;

    protected $options;

    protected $driver;

    protected $browser;

    protected $browserCallbacks;

    protected $stacks = [];

    protected $errors = [];

    protected $status = 200;


    public function __construct(Request $request, array $options = [], Collection $browserCallbacks = null)
    {
        $this->request = $request;
        $this->options = $options;
        $this->browserCallbacks = $browserCallbacks ?? collect();
    }

    public static function make(Request $request, array $options = [], Collection $browserCallbacks = null): HttpDusk
    {
        return new static($request, $options, $browserCallbacks);
    }

    public function createBrowser($url): void
    {
        $chromeOptions = new ChromeOptions;
        // ,'--window-size=375,667'
        $mobileEmulation = Arr::only($this->options, ['deviceMetrics']);
        $userAgent = Arr::get($this->options, 'userAgent', head((array)$this->request->header('User-Agent')));
        $chromeOptions->addArguments(['--headless', '--disable-gpu', '--lang=ja']);
        $mobileEmulation['userAgent'] = $userAgent;
        $chromeOptions->setExperimentalOption('mobileEmulation', $mobileEmulation);
        $driver = new Driver($chromeOptions);
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

    public function body()
    {
        return $this->browser()->getBody();
    }

    public function status()
    {
        return $this->status;
    }

    public function browser(): ChromeBrowser
    {
        if (!($this->browser instanceof ChromeBrowser)) {
            $this->createBrowser($this->request->url());
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
                }
                $this->stacks[] = $stack;
            } catch (Exception $e) {
                $this->failed($e);
            }
        });
    }

    public function response()
    {
        $this->browserCallback();
        return Http::response($this->body(), $this->status(), $this->header());
    }

    public static function http_dusk(HttpDuskFactory $duskRequest): Closure
    {
        return function (Request $request, array $options = []) use ($duskRequest) {
            return tap(static::make($request, $options, $duskRequest->browserCallbacks), function () use ($duskRequest) {
                $duskRequest->reset();
            })->response();
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
