<?php

namespace App;

use App\Review;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //manambahkan daftar yang ditampilkan
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'fullname', 'sex', 'avatar', 'user_id',
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

    public function review()
    {
        return $this->hasMany(Review::class);
    }
}
