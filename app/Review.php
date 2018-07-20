<?php

namespace App;

use App\Shop;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'rating', 'profile_id', 'shop_id',
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
