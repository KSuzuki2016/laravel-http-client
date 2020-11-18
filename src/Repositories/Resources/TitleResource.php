<?php


    namespace HttpClient\Repositories\Resources;

    use App\Crawler\Entities\LogMessageEntity;
    use HttpClient\Repositories\Entities\ExceptionProduct;
    use HttpClient\Repositories\Entities\ProductEntity;
    use HttpClient\Repositories\PostEpisodeListInterface;
    use HttpClient\Repositories\RouteResource;
    use Exception ;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;
    use Log;
    use Anilog ;
    use phpDocumentor\Reflection\Types\Boolean;


    abstract class TitleResource
    {

        protected $exception ;

        protected $routes ;

        protected $id ;

        protected $network_id ;

        protected $title ;

        protected $episodes = true ;

        public function __construct( RouteResource $routes )
        {
            $this->routes = $routes ;
            $this->logger = Log::channel('crawllog') ;
            $this->message  = new LogMessageEntity ;
            $this->title    = new ProductEntity ;
        }

        public function findById($id):self
        {
            $this->id = $id ;
            return $this ;
        }

        public function networkId():int
        {
            return $this->routes->current()->network_id ;
        }

        public function get():ProductEntity
        {
            try{
                return tap($this->resource($this->id),function(ProductEntity $title){
                    $this->title = $title ;
                }) ;
            }catch ( Exception $e ){
                return tap($this->error($e),function(ProductEntity $title){
                    $this->title = $title ;
                }) ;
            }
        }

        public function error( Exception $e ):ProductEntity
        {
            $this->message->increment('error') ;
            return new ExceptionProduct([
                'title'      => $this->id ,
                'network_id' => $this->networkId(),
                'is_error'   => 1 ,
                'crawl_log'  => $e->getMessage() ,
            ]) ;
        }

        abstract public function resource( $parameters = []  ):ProductEntity ;

        abstract public function episodes():PostEpisodeListInterface ;

        public function title():ProductEntity
        {
            return $this->get() ;
        }

        public function disableEpisode():void
        {
            $this->episodes = false ;
        }

        public function post():LogMessageEntity
        {
            $this->message->merge(new LogMessageEntity( $this->request( $this->title()->toArray() )->json())) ;

            if( $this->episodes && ! $this->title->is_error )
            {
                $this->episodes()->get()->map(function( $episode ){
                    $episode['title_name'] = $episode['title_name']??$this->title->name  ;
                    return $episode ;
                })->chunk(50)->map(function (Collection $episodes){
                    return $this->request(['title'  => $this->id ,
                        'network_id'    => $this->networkId(),
                        'episodes'      => $episodes->toArray() ]) ;
                })->each(function($item){
                    $this->message->merge(new LogMessageEntity($item->json())) ;
                }) ;
            }
            return $this->message ;
        }

        public function request( array $title )
        {
            return Anilog::resource('search.crawler.products.create')->request( array_filter( $title ,'filled' ) );
        }


    }
