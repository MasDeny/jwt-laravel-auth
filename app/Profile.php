<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //manambahkan daftar yang ditampilkan
    protected $tabel = 'profiles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'fullname', 'phone', 'status', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'membership', 'secret_code',
    ];

    
}
