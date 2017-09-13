<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
    	$this->validate($request, [
    		'name' => 'required',
    		'email' => 'required|email|unique:users,email',
    		'password' => 'required|min:6|confirmed',
    	]);
    	// dd($request->all());

    	// untuk menambahkan data user di tabel user
    	$user = User::create([
    		'name' 		=> request('name'),
    		'email' 	=> request('email'),
    		'password'	=> bcrypt(request('password'))
    	]);

    	$token = JWTAuth::fromUser($user);
    	return response()->json(['token_type' => 'Bearer ', 'token' => $token], 201);
    }

    public function login()
    {
    	$credentials = request()->only('email','password');

    	try
    	{
    		$token = JWTAuth::attempt($credentials);

    		if (!$token)
    		{
    			return response()->json(['error' => 'login gagal, periksa kembali username atau password'], 401);
    		}

    	}
    	catch(JWTException $e)
    	{
    		return response()->json(['error' => 'login gagal, periksa kembali koneksi anda'], 500);
    	}

    	return response()->json(['token_type' => 'Bearer ', 'token' => $token], 200);

    }

    public function edit()
    {

    }

    public function forget()
    {

    }
}