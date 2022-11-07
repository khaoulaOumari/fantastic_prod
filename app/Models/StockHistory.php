<?php
/**
 * File name: Food.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Models;

use Eloquent as Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class Food
 * @package App\Models
 * @version August 29, 2019, 9:38 pm UTC
 *
 * @property \App\Models\Restaurant restaurant
 * @property \App\Models\Category category
 * @property \Illuminate\Database\Eloquent\Collection[] discountables
 * @property \Illuminate\Database\Eloquent\Collection Extra
 * @property \Illuminate\Database\Eloquent\Collection Nutrition
 * @property \Illuminate\Database\Eloquent\Collection FoodsReview
 * @property string id
 * @property string name
 * @property double price
 * @property double discount_price
 * @property string description
 * @property string ingredients
 * @property double weight
 * @property boolean featured
 * @property double package_items_count
 * @property string unit
 * @property integer restaurant_id
 * @property integer category_id
 */
class StockHistory extends Model 
{
    

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public $table = 'stock_histories';
    public $fillable = [
        'quantity',
        'stock_id',
        'user_id',
        'task'
        
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        
    ];

   

    

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->where('custom_fields.in_table', '=', true)
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function stock()
    {
        return $this->belongsTo(\App\Models\Stock::class, 'stock_id', 'id');
    }

    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id')->select('id','name');
    }

    


   


}
