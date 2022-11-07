<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class Gallery
 * @package App\Models
 * @version August 29, 2019, 9:38 pm UTC
 *
 * @property \App\Models\Restaurant restaurant
 * @property string description
 * @property integer restaurant_id
 */
class Icon extends Model implements HasMedia
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    public $table = 'icons';
    


    public $fillable = [
        'code',
        'icon',
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'icon' => 'string',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    

    /**
     * New Attributes
     *
     * @var array
     */
    

   


    
}
