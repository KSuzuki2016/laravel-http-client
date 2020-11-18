<?php


    namespace HttpClient\Repositories\Resources;

    use App\Crawler\Entities\LogMessageEntity;
    use App\Crawler\Models\NetworkModel;
    use HttpClient\Repositories\Entities\ProductEntity;
    use HttpClient\Repositories\RouteResource;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Support\Collection;
    use Log ;
    use Anilog;

    abstract class TitleListResource
    {
        protected $routes ;
        protected $crawler ;
        protected $network ;
        protected $logger ;
        protected $message ;
        protected $listParameters = [] ;

        public function __construct( RouteResource $routes )
        {
            $this->routes = $routes ;
            $this->network = NetworkModel::query() ;
            $this->logger = Log::channel('crawllog') ;
            $this->message = new LogMessageEntity ;
        }

        public function networkId():int
        {
            return $this->routes->current()->network_id ;
        }

        /**
         * TitleResourceでいうところのfindById
         * リストリクエストに対してのパラメーター設定
         * 例えばページングなど
         *
         * @param array $listParameters
         * @return $this
         */
        public function parameters($listParameters = [] ):self
        {
            $this->listParameters = $listParameters ;
            return $this ;
        }

        public function get():Collection
        {
            return $this->filter() ;
        }

        public function filter(array $parameters = []):Collection
        {
            return $this->resource($parameters) ;
        }

        abstract public function resource( $parameters = []  ):Collection ;

        public function titles():Collection
        {
            return $this->get() ;
        }

        public function post():LogMessageEntity
        {
            $this->titles()->chunk(100)->each(function ($titles){
                $this->message->merge( $this->request( $titles->toArray() )->json() ) ;
            }) ;
            tap($this->network->findOrFail( $this->networkId() ),function($network){
                $network->listUpdate();
            }) ;
            return $this->message ;
        }

        public function request( array $titles )
        {
            return Anilog::resource('search.crawler.products.bulk')->request( array_filter( $titles ,'filled' ) ) ;
        }

    }
