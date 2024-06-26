<?php

namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverCapabilities;
use Illuminate\Support\Str;
use KSuzuki2016\HttpClient\Contracts\DriverInterface;
use Laravel\Dusk\OperatingSystem;
use Symfony\Component\Process\Process;

/**
 * Class ChromeDriver
 * @package KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome
 */
class ChromeDriver implements DriverInterface
{
    /** @var string */
    private $driver;

    /** @var int */
    private $port = 9515;

    /** @var Process<int, string>|null */
    private $process;

    /** @var WebDriverCapabilities */
    private $capabilities;

    /**
     * ChromeDriver constructor.
     * @param ChromeOptions $options
     * @param null $binPath
     */
    public function __construct(ChromeOptions $options, $binPath = null)
    {
        $this->setBinary($binPath);
        $this->start();
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability('chromeOptions', $options);
        $this->setCapabilities($capabilities);
    }

    /**
     * @param null $path
     */
    protected function setBinary($path = null): void
    {
        if ($path) {
            $path = Str::finish($path, '/');
            if ($this->onWindows()) {
                $this->driver = realpath($path . 'chromedriver-win.exe');
            } elseif ($this->onMac()) {
                $this->driver = realpath($path . 'chromedriver-mac');
            } else {
                $this->driver = realpath($path . 'chromedriver-linux');
            }
        }
    }

    /**
     * @return bool
     */
    protected function onWindows(): bool
    {
        return OperatingSystem::onWindows();
    }

    /**
     * @return bool
     */
    protected function onMac(): bool
    {
        return OperatingSystem::onMac();
    }

    /**
     * {@inheritDoc}
     */
    public function setCapabilities(WebDriverCapabilities $capabilities): void
    {
        $this->capabilities = $capabilities;
    }


    /**
     * {@inheritDoc}
     */
    public function getDriver(): RemoteWebDriver
    {
        return RemoteWebDriver::create("http://localhost:{$this->port}", $this->capabilities);
    }


    /**
     * Start the Chromedriver process.
     *
     * @return $this
     */
    public function start(): DriverInterface
    {
        if (!$this->process) {
            $this->process = (new ChromeProcess($this->driver, $this->port))->toProcess();
            $this->process->start();
            sleep(1);
        }

        return $this;
    }


    /**
     * Ensure the driver is closed by the upstream library.
     *
     * @return $this
     */
    public function stop(): DriverInterface
    {
        if ($this->process) {
            $this->process->stop();
            unset($this->process);
        }

        return $this;
    }


    /**
     * Automatically end the driver when this class is done with.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->stop();
    }
}
