<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    //
    protected $table = Shops;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_name', 'owner', 'address', 'description', 'photo',
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
}
