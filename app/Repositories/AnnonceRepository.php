<?php

namespace App\Repositories;

use App\Models\Annonce;
use Illuminate\Container\Container as Application;
use InfyOm\Generator\Common\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


/**
 * Class AnnonceRepository.
 *
 * @package namespace App\Repositories;
 */
class AnnonceRepository extends BaseRepository implements CacheableInterface
{

    use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return string
     */

    protected $fieldSearchable = [
        'name',
        'text',
        'type',
        'start_date',
        'end_date'
    ];
    public function model()
    {
        return Annonce::class;
    }

    

    
}
