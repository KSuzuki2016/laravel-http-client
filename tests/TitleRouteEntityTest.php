<?php

namespace HttpClient\Tests;

use Tests\TitleRoute;
use PHPUnit\Framework\TestCase;

class TitleRouteEntityTest extends TestCase
{

    private $set_string = 'set_string' ;

    private $group      = 'group' ;

    public function routeEntity( array $attribute = [] )
    {
        return new TitleRoute($attribute) ;
    }

    public function testBaseRouteEntity()
    {
        $this->testGroupRouteEntity() ;
    }

    public function testGroupRouteEntity()
    {
        $entity = $this->routeEntity() ;
        $this->assertSame( $entity->group , $this->group ) ;
        $this->assertSame( $entity->group() , $this->group ) ;

        $entity->group = $this->set_string ;
        $this->assertSame( $entity->group , $this->set_string );
        $this->assertSame( $entity->group() , $this->set_string );
    }
}
