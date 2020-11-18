<?php


    namespace HttpClient\Repositories\Resources;


    use HttpClient\Repositories\PostEpisodeListInterface;
    use HttpClient\Repositories\RouteResource;
    use Illuminate\Support\Collection;

    abstract class EpisodeListResource implements PostEpisodeListInterface
    {
        protected $routes ;

        protected $title ;

        protected $title_name ;

        protected $network_id ;

        protected $id ;

        protected $listParameters = [] ;

        public function __construct( RouteResource $routes , $title = null )
        {
            $this->routes = $routes ;
            $this->title  = $title ;
        }

        public function title($title):self
        {
            $this->title = $title ;
            return $this ;
        }

        public function getTitleId()
        {
            return $this->title;
        }

        /**
         * @param mixed $title_name
         * @return EpisodeListResource
         */
        public function setTitleName($title_name)
        {
            $this->title_name = $title_name;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getTitleName($default=null)
        {
            return $this->title_name??$default ;
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
            return $this->filter([$this->title]) ;
        }

        public function filter(array $parameters = []):Collection
        {
            return tap($this->resource($parameters),function() {
                $this->network_id = $this->routes->current()->network_id ;
            }) ;
        }

        abstract public function resource( $parameters = []  ):Collection ;

    }
