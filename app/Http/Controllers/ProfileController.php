<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //menambahkan fungsi untuk membuat profile
    public function add(Request $request)
    {
    	$profile = Profile::create([
    	
    	]);
    }
}
