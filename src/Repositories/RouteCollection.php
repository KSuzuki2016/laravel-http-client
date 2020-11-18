<?php


    namespace HttpClient\Repositories;


    use App\Anilog\Enums\NetworkEnum;
    use HttpClient\Repositories\Entities\RouteEntity;
    use HttpClient\Repositories\Entities\UrlEntity;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Str;

    class RouteCollection
    {
        public $routes ;

        public $network_id ;

        public function __construct( Collection $routes = null )
        {
            if( $routes instanceof Collection ) {
                $this->routes = $routes ;
            } else {
                $this->routes = new Collection ;
            }
        }

        public function new( Collection $routes = null ):RouteCollection
        {
            return new static( $routes ) ;
        }

        public function get( $uri , $name , $action = null )
        {
            $this->addRoute( $this->route( $name, 'GET' , $uri , $action ) );
        }

        public function add( $method , $uri , $name , $action )
        {
            $this->addRoute( $this->route( $name , $method , $uri , $action ) );
        }
        public function addRoute( UrlEntity $route )
        {
            $this->routes->push( $route ) ;
        }

        public function hasNetwork($network)
        {
            if( $network instanceof NetworkEnum) {
                $network = $network->key ;
            }
            return new static( $this->routes->where('network_id' , (int) $network ) ) ;
        }

        public function count()
        {
            return $this->routes->count() ;
        }
        public function isEmpty()
        {
            return $this->routes->isEmpty() ;
        }
        public function isNotEmpty()
        {
            return $this->routes->isNotEmpty() ;
        }

        public function filter_domain($string):Collection
        {
            return $this->routes->filter(function(UrlEntity $route ) use($string){
                return Str::startsWith( Str::after($route->uri,'://') , $string );
            }) ;
        }
        public function getByName($name)
        {
            return $this->routes->firstWhere('name' ,$name ) ;
        }
        public function getByAction($action)
        {
            return $this->routes->firstWhere('action' , $action ) ;
        }

        public function group(array $groups , callable $callback = null ) {
            if( is_callable($callback))
            {
                $routeCollection = new static ;
                $callback( $routeCollection ) ;
                foreach ($groups as $group => $value ){
                    $routeCollection->routes = static::setGroup( $routeCollection->routes , $group , $value ) ;
                }
                $merged = $this->routes->merge( $routeCollection->routes ) ;
            } else {
                foreach ($groups as $group => $value ){
                    $this->routes = static::setGroup( $this->routes , $group , $value ) ;
                }
                $merged = $this->routes->merge( $this->routes ) ;
            }
            return $this->routes = $merged ;
        }

        protected static function setGroup( Collection $routeCollection , $key , $value ) {
            return $routeCollection->map(function(UrlEntity $route) use ($key , $value){
                $route->set($key,$value);
                return $route ;
            });
        }

        protected function route( $name = null , $method = null , $uri = null , $action = null , $group = null )
        {
            return new UrlEntity([
                'name'          => $name ,
                'method'        => $method ,
                'uri'           => $uri ,
                'action'        => $action ,
                'group'         => $group ] ) ;
        }
    }
