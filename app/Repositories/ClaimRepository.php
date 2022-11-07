<?php

namespace App\Repositories;

use App\Models\Claim;
use Illuminate\Container\Container as Application;
use InfyOm\Generator\Common\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


/**
 * Class AnnonceRepository.
 *
 * @package namespace App\Repositories;
 */
class ClaimRepository extends BaseRepository implements CacheableInterface
{

    use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return string
     */

    protected $fieldSearchable = [
        'title',
        'status_id'
    ];
    public function model()
    {
        return Claim::class;
    }

    

    
}
