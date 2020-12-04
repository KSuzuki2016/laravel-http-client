<?php

namespace KSuzuki2016\HttpClient\Drivers;

use Laravel\Dusk\Chrome\ChromeProcess as BaseChromeProcess;
use Symfony\Component\Process\Process;

class ChromeProcess extends BaseChromeProcess
{
    /**
     * The port to run the Chromedriver on.
     *
     * @var int
     */
    private $port;

    /**
     * Create a new instance.
     *
     * @param int $port The port to run on
     */
    public function __construct($driver = null, int $port = null)
    {
        parent::__construct($driver);
        $this->port = $port ?: 9515;
    }


    /**
     * Build the Chromedriver with Symfony Process.
     *
     * @param array<string> $arguments
     *
     * @return Process<int, string>
     */
    public function toProcess(array $arguments = []): Process
    {
        $arguments[] = "--port={$this->port}";
        return parent::toProcess($arguments);
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
}
