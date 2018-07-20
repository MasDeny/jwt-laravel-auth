<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_name', 'owner', 'address', 'description', 'avatar', 'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->hasMany(Products::class);
    }
    public function review()
    {
        return $this->hasMany(Review::class);
    }
    public function location()
    {
        return $this->hasOne(Location::class);
    }
}
