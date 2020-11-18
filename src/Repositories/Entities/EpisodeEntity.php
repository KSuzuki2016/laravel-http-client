<?php


    namespace HttpClient\Repositories\Entities;


    use HttpClient\Repositories\Entity;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Collection;

    /**
     * Class EpisodeEntity
     *
     * @property string episode
     * @property string slug
     * @property int network_id
     * @property string title
     * @property string title_name
     * @property string episode_name
     * @property string episode_number
     * @property string image
     * @property string description
     *
     *
     * @package HttpClient\Repositories\Entities
     */
    class EpisodeEntity extends Entity
    {
        protected $fillable = [
            'episode','slug','title','network_id',
            'title_name','episode_name','episode_number',
            'image','description'] ;


    }
