<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //manambahkan daftar yang ditampilkan
    //protected $table = 'profiles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'fullname', 'phone', 'status', 'avatar', 'secret_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'membership',
    ];
   
}
