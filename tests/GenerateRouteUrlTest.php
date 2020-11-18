<?php

namespace HttpClient\Tests;

use HttpClient\Repositories\Entities\RouteEntity;
use HttpClient\Repositories\RouteUrlGenerator;
use PHPUnit\Framework\TestCase;

class GenerateRouteUrlTest extends TestCase
{
    private $urlTemplate = 'https://anime.dmkt-sp.jp/{route}/ci?workId={title}' ;

    private $default = [
        'title' => 'title_default',
        'route' => 'route_default'
    ] ;

    private $route ;

    protected function setUp(): void
    {
        parent::setUp();
        $this->route = new RouteEntity ;
        $this->route->uri = $this->urlTemplate ;
    }

    public function testUseDefaultParameter()
    {
        $this->route->defaultParameters = $this->default ;
        $url = RouteUrlGenerator::to( $this->route , [
            'title'     => 'title' ,
            'route'     => 'route' ,
        ] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route/ci?workId=title');

        $url = RouteUrlGenerator::to( $this->route , [
            'title'     => 'title' ,
        ] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route_default/ci?workId=title');

        $url = RouteUrlGenerator::to( $this->route , [
            'route'     => 'route' ,
            'episode'   => 'episode' ,
        ] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route/ci?workId=title_default&episode=episode');

        $url = RouteUrlGenerator::to( $this->route , [
            'episode'   => 'episode' ,
        ] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route_default/ci?workId=title_default&episode=episode');

        $url = RouteUrlGenerator::to( $this->route , [
            'title'   => 'title' ,
            'episode'   => 'episode' ,
            'workId'   => 'extract' ,
        ] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route_default/ci?workId=title&episode=episode');

        $url = RouteUrlGenerator::to( $this->route , ['route','episode'] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route/ci?workId=episode');

        $url = RouteUrlGenerator::to( $this->route , ['route','episode','extract'] ) ;
        $this->assertSame($url,'https://anime.dmkt-sp.jp/route/ci?workId=episode');




    }
}
