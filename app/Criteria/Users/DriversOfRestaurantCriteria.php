<?php
/**
 * File name: DriversOfRestaurantCriteria.php
 * Last modified: 2020.05.09 at 14:02:59
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Criteria\Users;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DriversOfRestaurantCriteria.
 *
 * @package namespace App\Criteria\Users;
 */
class DriversOfRestaurantCriteria implements CriteriaInterface
{
    /**
     * @var int
     */
    private $restaurantId;

    /**
     * DriversOfRestaurantCriteria constructor.
     */
    public function __construct($restaurantId)
    {
        $this->restaurantId = $restaurantId;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        // return $model->join('driver_restaurants','users.id','=','driver_restaurants.user_id')
        //     ->whereIn('driver_restaurants.restaurant_id',$this->restaurantId);

        return $model->join('driver_restaurants','users.id','=','driver_restaurants.user_id')
            ->join('drivers','users.id','drivers.user_id')
            ->where('driver_restaurants.restaurant_id',$this->restaurantId)
            ->where('drivers.available',1)
            ->where('drivers.available_delivery',1)
            ->select('users.*');
    }
}
