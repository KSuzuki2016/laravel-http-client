<?php


    namespace HttpClient\Repositories\Resources;

    use Http ;
    use HttpClient\Repositories\Entities\EpisodeEntity;
    use HttpClient\Repositories\RouteResource;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;
    use Symfony\Component\DomCrawler\Crawler;

    abstract class EpisodeResource
    {
        protected $routes ;

        protected $title ;

        protected $title_name ;

        protected $network_id ;

        protected $id ;

        public function __construct( RouteResource $routes , $title = null  )
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
         * @return EpisodeResource
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

        public function get():EpisodeEntity
        {
            return $this->resource([$this->title,$this->id]) ;
        }

        public function findById($id):self
        {
            $this->id = $id ;
            return $this ;
        }

        abstract public function resource( $parameters = []  ):EpisodeEntity ;

    }
