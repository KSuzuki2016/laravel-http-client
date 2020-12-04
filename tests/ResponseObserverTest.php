<?php

namespace Tests;


use Tests\Observers\ReplaceResponseObserver;

class ResponseObserverTest extends TestCase
{

    /** @test */
    public function check_observer()
    {
        $text = 'Replace Json';
        $response = $this->manager
            ->responseObserver(new ReplaceResponseObserver($text))
            ->responseObserver(new ReplaceResponseObserver)
            ->get($this->json);
        self::assertSame($text, $response->json('text'));
        self::assertSame(2, $response->json('counter'));
    }
}
