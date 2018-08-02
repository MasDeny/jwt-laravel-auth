<?php

namespace App;

use App\Products;
use Illuminate\Database\Eloquent\Model;

class Products_photos extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
    	'filename', 'mime', 'original_filename', 'products_id',
    ];

    public function products()
    {
        return $this->belongsTo(Products::class);
    }
}
