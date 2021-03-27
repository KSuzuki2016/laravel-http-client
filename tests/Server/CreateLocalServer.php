<?php

namespace Tests\Server;

/**
 * Trait CreateLocalServer
 * @package Tests\Server
 */
trait CreateLocalServer
{
    /**
     * @var
     */
    public $localServer;

    /**
     * @param $documentRoot
     * @param string $host
     * @return void
     */
    public function createLocalServer($documentRoot, $host = 'localhost:8000'): void
    {
        if (!$this->localServer) {
            $this->localServer = LocalServer::make($documentRoot, $host);
        }
    }
}
