<?php


    namespace HttpClient\Repositories\Entities;


    class ExceptionProduct extends ProductEntity
    {
        protected $attributes = [
            'need_crawl'    => 0 ,
            'available'     => 0 ,
            'is_error'      => 1 ,
        ] ;

    }
