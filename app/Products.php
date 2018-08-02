<?php

namespace App;

use App\Products_photos;
use App\Shop;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_name', 'product_type', 'price', 'shop_id',
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

    public function products_photos()
    {
    	return $this->hasOne(Products_photos::class);
    }
}
