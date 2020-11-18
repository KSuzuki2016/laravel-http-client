<?php


    namespace HttpClient\Repositories\Entities;


    use HttpClient\Repositories\Entity;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Collection;

    /**
     * Class RouteListEntity
     *
     * @property int network_id
     * @property string route_name
     * @property array parameters
     * @property bool active
     *
     *
     * @package HttpClient\Repositories\Entities
     */
    class RouteListEntity extends Entity
    {
        protected $attributes = [
            'network_id' => null ,
            'route_name' => '' ,
            'parameters' => [] ,
            'active' => true ,
        ] ;

        protected $fillable = [ 'network_id','route_name','parameters','active' ] ;


    }
