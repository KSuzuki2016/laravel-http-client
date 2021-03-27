<?php

namespace Tests\Server;


use Symfony\Component\Process\Process;

/**
 * Class LocalServer
 * @package Tests\Server
 */
class LocalServer
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * LocalServer constructor.
     * @param null $host
     * @param null $port
     * @param null $documentRoot
     */
    public function __construct($host = null, $port = null, $documentRoot = null)
    {
        $documentRoot = $documentRoot ?: __DIR__ . '/pages';
        $host = str_replace(['http://', 'https://'], ['', ''], (string)$host);
        if (str_contains($host, ':')) {
            [$host, $port] = explode(':', $host);
        }
        $this->process = new Process(['php', '-S', ($host ?: 'localhost') . ':' . ($port ?: '8000')], $documentRoot);
    }

    /**
     * @param null $documentRoot
     * @param null $host
     * @param null $port
     * @return static
     */
    public static function make($documentRoot = null, $host = null, $port = null): self
    {
        return (new self($host, $port, $documentRoot))->up();
    }

    protected function up(): self
    {
        if ($this->process instanceof Process && !$this->process->isRunning()) {
            $this->process->start();
        }
        return $this;
    }

    public function __destruct()
    {
        $this->down();
    }

    /**
     * @return void
     */
    protected function down(): void
    {
        if ($this->process instanceof Process) {
            $this->process->stop();
        }
        unset($this->process);
    }
}
