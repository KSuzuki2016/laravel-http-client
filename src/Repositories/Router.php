<?php


    namespace HttpClient\Repositories;

    use HttpClient\Interfaces\RouterInterface;
    use HttpClient\Repositories\Entities\RouteEntity;
    use HttpClient\Repositories\Entities\UrlEntity;
    use HttpClient\Repositories\Exceptions\UrlGenerationException;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Str;
    use Spatie\Url\Url;

    class Router implements RouterInterface
    {
        protected $routeCollection ;

        protected $headers = ['Method','URI','Name','Source','Resource'] ;

        public function __construct( RouteCollection $routeCollection )
        {
            $this->routeCollection = $routeCollection ;
        }

        /**
         * @return string[]
         */
        public function getHeaders( bool $compact = false ): array
        {
            return array_slice( $this->headers , 0 , $compact?3:5 ) ;
        }

        /**
         * @param string[] $headers
         */
        public function setHeaders(array $headers): void
        {
            $this->headers = $headers;
        }

        public function filter( $key , $search ):RouteCollection
        {
            return $this->routeCollection->new( $this->routeCollection->routes->filter(function($route) use( $key , $search ){
                return Str::contains( $route->getAttribute($key) , $search ) ;
            }) ) ;
        }

        public function instanceOf(string $type):RouteCollection
        {
            return $this->routeCollection->new( $this->routeCollection->routes->whereInstanceOf($type) ) ;
        }

        public function reject( $key , $search ):RouteCollection
        {
            return $this->routeCollection->new( $this->routeCollection->routes->filter(function($route) use( $key , $search ){
                return ! Str::contains( $route->getAttribute($key) , $search ) ;
            }) ) ;
        }

        public function transform( array $columns = [] ):Collection
        {
            if( blank($columns) ) $columns = $this->getHeaders() ;

            return $this->routeCollection->routes->map(function($route) use( $columns ){
                $row = [] ;
                foreach ( $columns as $column ){
                    $row[] = $route->getAttribute( strtolower($column) ) ;
                }
                return $row ;
            }) ;
        }

        public function getByName($name)
        {
            return tap( $this->routeCollection->getByName($name) , function($route) use($name){
                if( empty($route) )
                throw_unless( $route , UrlGenerationException::routeNotDefinedException($name) ) ;
            }) ;
        }

        public function getByAction($action)
        {
            return tap( $this->routeCollection->getByAction($action) , function($route) use($action){
                throw_unless( $route , UrlGenerationException::routeActionNotDefinedException($action) ) ;
            }) ;
        }

        public function hasNetwork($network):RouterInterface
        {
            return new static( tap($this->routeCollection->hasNetwork($network),function(RouteCollection $routes) use ($network){
                throw_if( $routes->isEmpty() , UrlGenerationException::hasNotRouteGroupDefinedException( 'Network ['. (string)$network .']') ) ;
            }) ) ;
        }

        public function matchUrl($string_url):?UrlEntity
        {
            $url = Url::fromString($string_url) ;
            return $this->routeCollection
                        ->filter_domain( $url->getHost() )
                        ->first( function(UrlEntity $route) use( $url ) {
                            $routeUri = Url::fromString($route->uri) ;
                            $pattern = '/^'. preg_replace('/\\\\{[^\\\\}]+\\\\}/','(.+)', preg_quote($routeUri->getPath(),'/') ) .'$/u' ;
                            if( preg_match( $pattern , $url->getPath() , $match ) ) {
                                unset($match[0]);
                                $route->defaultParameters = array_values($match) ;
                                $route->url = (string) $url ;
                                return true ;
                            }
                            return false ;
                        }) ;
        }

    }
