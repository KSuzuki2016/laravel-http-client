<?php


    namespace HttpClient\Interfaces;


    use HttpClient\Repositories\Entities\UrlEntity;
    use HttpClient\Repositories\RouteCollection;
    use Illuminate\Support\Collection;

    interface RouterInterface
    {
        public function getByName($name) ;

        public function getByAction($action) ;

        public function hasNetwork($network):RouterInterface ;

        public function matchUrl($string_url):?UrlEntity ;

        public function transform( array $columns = [] ):Collection;

        public function filter( $key , $search ):RouteCollection ;

        public function instanceOf(string $type):RouteCollection ;

        public function reject( $key , $search ):RouteCollection ;

        public function getHeaders( bool $compact = false ): array ;

        public function setHeaders(array $headers): void ;
    }
