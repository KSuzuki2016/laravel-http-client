<?php


    namespace HttpClient\WebDriver ;

    use Facebook\WebDriver\Chrome\ChromeOptions;
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use duncan3dc\Laravel\Drivers\DriverInterface;
    use Facebook\WebDriver\WebDriverCapabilities;
    use duncan3dc\Laravel\Drivers\ChromeProcess ;
    use Symfony\Component\Process\Process;

    class Driver  implements DriverInterface
    {
        public $port = 9515 ;

        /** @var Process<int, string>|null */
        public $process;

        public $capabilities;

        public function __construct( ChromeOptions $options )
        {
            $this->start();
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability('chromeOptions' , $options ) ;
            $this->setCapabilities($capabilities);
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
            return RemoteWebDriver::create("http://localhost:" . $this->port , $this->capabilities );
        }


        /**
         * Start the Chromedriver process.
         *
         * @return $this
         */
        public function start(): DriverInterface
        {
            if (!$this->process) {
                $this->process = (new ChromeProcess($this->port))->toProcess();
                $this->process->setTimeout(600);
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
