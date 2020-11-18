<?php


    namespace HttpClient\Repositories\Entities;


    use HttpClient\Repositories\Entity;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Collection;

    /**
     * Class ProductEntity
     *
     * @property string network_id
     * @property string title
     * @property string slug
     * @property string name
     * @property string yomi
     * @property integer category
     * @property integer number
     * @property string number_name
     * @property string description
     * @property string first_year
     * @property string first_month
     * @property string image
     * @property boolean available
     * @property boolean is_error
     * @property Carbon last_crawl
     * @property int need_crawl
     * @property Collection seasons
     * @property array casts
     * @property array staffs
     * @property array links
     * @property array tags
     * @property string crawl_log
     *
     * @package HttpClient\Repositories\Entities
     */
    class ProductEntity extends Entity
    {
        protected $attributes = [
            'need_crawl'    => 0 ,
            'available'     => 1 ,
            'is_error'      => 0 ,
        ] ;

        protected $fillable = [
            'title','slug','network_id','category',
            'name','yomi','number','number_name',
            'first_year','first_month','image','description','available',
            'is_error','last_crawl','seasons','casts','staffs','links','tags','crawl_log'
        ] ;

        public function name(): string
        {
            return $this->name??'name';
        }


    }
