<?php

namespace Tests\Server;


use Symfony\Component\Process\Process;

class LocalServer
{
    protected $process;

    public function __construct($host = null, $port = null, $documentRoot = null)
    {
        $documentRoot = $documentRoot ? $documentRoot : __DIR__ . '/pages';
        $host = str_replace(['http://', 'https://'], ['', ''], (string)$host);
        if (str_contains($host, ':')) {
            list($host, $port) = explode(':', $host);
        }
        $this->process = new Process(['php', '-S', ($host ? $host : 'localhost') . ':' . ($port ? $port : '8000')], $documentRoot);
    }

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

    protected function down()
    {
        if ($this->process instanceof Process) {
            $this->process->stop();
        }
        unset($this->process);
    }
}
