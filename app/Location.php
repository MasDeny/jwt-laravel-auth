<?php

namespace App;

use App\Shop;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $timestamps = false;
    
    protected $fillable = [
    	'name_location', 'lat', 'long', 'shop_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
