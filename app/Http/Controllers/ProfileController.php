<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    //menambahkan fungsi untuk membuat profile
    public function create(Request $request)
    {
    	//validasi profile
    	$this->validate($request, [
    		'fullname' => 'required',
    		'phone' => 'required|max:13|min:11',
    	]);

    	//inisialisasi token untuk user_id 
    	$user = JWTAuth::toUser(JWTAuth::getToken());

   
    	// menambahkan field dalam tabel profile
    	$profile = $user->profile()->create([
    	'fullname'		=> request('fullname'),
    	'phone'			=> request('phone'),
    	'status'		=> request('status'),
    	'secret_code'	=> str_random(5),
    	]);

    	return $profile;
    	
    }
}
