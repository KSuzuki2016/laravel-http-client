<?php

namespace Tests\Server;

trait CreateLocalServer
{
    public $localServer;

    public function createLocalServer($documentRoot, $host = 'localhost:8000')
    {
        if (!$this->localServer) {
            $this->localServer = LocalServer::make($documentRoot, $host);
        }
    }
}
