<?php


    namespace HttpClient;


    use Illuminate\Http\Client\Factory;
    use Closure ;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;

    class DuskRequest extends Factory
    {
        protected $recording = false ;


        public function __construct()
        {
            parent::__construct() ;
            $this->duskCallbacks    = collect() ;

        }

        /**
         * The stub callables that will handle requests.
         *
         * @var Collection
         */
        public $browserCallbacks ;

        public $duskCallbacks ;

        public function dusk($callback = null)
        {
            $this->browserCallbacks = collect() ;
            //$this->recorded         = [] ;
            $this->duskUrl('*', HttpDusk::http_dusk($this) );
            if( is_callable( $callback ) ) {
                $this->browserCallback($callback) ;
            }
            return $this ;
        }

        public function duskUrl($url,$callback):void
        {
            if( $this->duskCallbacks->where('url' , $url )->isEmpty() )
            {
                $this->duskCallbacks->push(['url' => $url ]) ;
                $this->stubUrl($url,$callback);
            }
        }

        public function reset(){
            //$this->recorded = [] ;
            App::clearResolvedInstances();
        }
        public function browserCallback(callable $callback)
        {
            $this->browserCallbacks->push($callback);
            return $this;
        }
    }
