<?php

namespace App;

use App\Profile;
use App\Shop;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'code', 'phone', 'status', 'status_user',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function shop()
    {
        return $this->hasOne(Shop::class);
    }
}
