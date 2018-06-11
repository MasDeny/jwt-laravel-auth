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
    public function product()
    {
        return $this->hasMany(Product::class);
    }
    public function review()
    {

    }
    public function location()
    {
        return $this->hasMany(Product::class);
    }
}
