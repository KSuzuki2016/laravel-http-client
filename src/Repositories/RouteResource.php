<?php


    namespace HttpClient\Repositories;


    use App\Anilog\Enums\NetworkEnum;
    use App\Crawler\Models\NetworkTitleModel;
    use HttpClient\Interfaces\RouterInterface;
    use HttpClient\Repositories\Entities\RouteEntity;
    use HttpClient\Repositories\Entities\UrlEntity;
    use HttpClient\Repositories\Exceptions\UrlGenerationException;
    use HttpClient\Repositories\Resources\TitleListResource;
    use HttpClient\Repositories\Resources\TitleResource;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Support\Traits\Macroable;
    use Spatie\Url\Url;


    class RouteResource
    {
        use Macroable {
            __call as macroCall;
        }

        protected $routes ;

        protected $route ;

        public function __construct( RouterInterface $routes )
        {
            $this->routes   = $routes ;
            $this->route    = new RouteEntity ;
        }

        /**
         * routeからHTTPレスポンスを取得
         *
         * @param $name
         * @param array $parameters
         * @return mixed
         * @throws UrlGenerationException
         */
        public function source($name, $parameters = [])
        {
            return $this->route($name)->generateUrl($parameters)->source() ;
        }

        /**
         * routeからリソースを取得
         *
         * @param $name
         * @return Application|mixed
         * @throws Exceptions\RouteResourceException
         * @throws UrlGenerationException
         */
        public function resource($name)
        {
            return $this->route($name)->resource() ;
        }

        public function getTitleListResource( $network ):TitleListResource
        {
            return $this->resource( $this->getNetworkName( $network ) . '.title.list' ) ;
        }
        public function getTitleResource( $network ):TitleResource
        {
            return $this->resource( $this->getNetworkName( $network ) . '.title' ) ;
        }

        public function getNetworkTitleResource( NetworkTitleModel $title ):TitleResource
        {
            return $this->getTitleResource( $title->network_id )->findById( $title->title ) ;
        }

        public function route($name):RouteEntity
        {
            $this->route = $this->routes->getByName($name) ;
            return $this->route ;
        }

        public function hasRouteName($name):bool
        {
            return filled( $this->routes->getByName($name) ) ;
        }

        public function network($network):RouteResource
        {
            return new static( $this->routes->hasNetwork($network) ) ;
        }

        /**
         * @return mixed
         */
        public function current():RouteEntity
        {
            return $this->route ;
        }

        // やること : ここのクラスからネットワークでの絞り込みとグループ検索

        public function url( $name, $parameters = [])
        {
            return $this->toRoute( $this->route($name) , $parameters ) ;
        }

        public function toRoute( $route , $parameters )
        {
            return RouteUrlGenerator::to( $route , $parameters ) ;
        }


        protected function getNetworkName( $network )
        {
            return NetworkEnum::getSnakeKey((int)$network) ;
        }

        public function matchUrl($string_url):?UrlEntity
        {
            return $this->routes->matchUrl( $string_url ) ;
        }
    }
