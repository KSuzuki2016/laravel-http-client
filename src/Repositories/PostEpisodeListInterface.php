<?php


    namespace HttpClient\Repositories;


    use Illuminate\Support\Collection;

    interface PostEpisodeListInterface
    {
        public function get():Collection ;
    }
