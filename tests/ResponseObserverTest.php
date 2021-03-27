<?php

namespace Tests;


use Tests\Observers\ReplaceResponseObserver;

/**
 * Class ResponseObserverTest
 * @package Tests
 */
class ResponseObserverTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function check_observer(): void
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
