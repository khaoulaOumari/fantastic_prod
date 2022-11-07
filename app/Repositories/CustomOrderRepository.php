<?php

namespace App\Repositories;

use App\Models\CustomOrder;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FoodOrderRepository
 * @package App\Repositories
 * @version August 31, 2019, 11:18 am UTC
 *
 * @method FoodOrder findWithoutFail($id, $columns = ['*'])
 * @method FoodOrder find($id, $columns = ['*'])
 * @method FoodOrder first($columns = ['*'])
*/
class CustomOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'quantite',
        'order_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomOrder::class;
    }
}
