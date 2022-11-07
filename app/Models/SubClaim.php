<?php
/**
 * File name: Order.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Models;

use Eloquent as Model;

/**
 * Class Order
 * @package App\Models
 * @version August 31, 2019, 11:11 am UTC
 *
 * @property \App\Models\User user
 * @property \App\Models\DeliveryAddress deliveryAddress
 * @property \App\Models\Payment payment
 * @property \App\Models\OrderStatus orderStatus
 * @property \App\Models\FoodOrder[] foodOrders
 * @property integer user_id
 * @property integer order_status_id
 * @property integer payment_id
 * @property double tax
 * @property double delivery_fee
 * @property string id
 * @property int delivery_address_id
 * @property string hint
 */
class SubClaim extends Model
{

    public $table = 'sub_claims';
    


    public $fillable = [
        'claim_id',
        'text'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'claim_id' => 'integer',
        'text' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'claim_id' => 'required|exists:claims,id',
        'text' => 'required',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        
    ];

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
        if (!$hasCustomField){
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
            ->where('custom_fields.in_table','=',true)
            ->get()->toArray();

        return convertToAssoc($array,'name');
    }

    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function claim()
    {
        return $this->belongsTo(\App\Models\Claim::class, 'claim_id', 'id');
    }

    


    public function subClaimsOrders()
    {
        return $this->hasMany(\App\Models\SubClaimOrder::class);
    }

    
    
}
