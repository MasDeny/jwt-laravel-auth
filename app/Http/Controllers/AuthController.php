<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use JWTAuth;
use Keygen\Keygen;
use Nasution\ZenzivaSms\Client as Sms;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
    	$this->validate($request, [
    		'username'    => 'required',
    		'email'       => 'email|unique:users,email',
    		'password'    => 'required|min:6|confirmed',
            'phone'       => 'min:10|max:13',
            'status'      => 'required|in:seller,buyer',
    	]);
    	// dd($request->all());
        $key_code = Keygen::numeric(5)->generate();
    	// untuk menambahkan data user di tabel user
    	$user = User::create([
    		'username' 	=> request('username'),
    		'email' 	=> request('email'),
    		'password'	=> bcrypt(request('password')),
            'phone'     => request('phone'),
            'code'      => $key_code,
            'status'    => request('status'),
    	]);

    	$token = JWTAuth::fromUser($user);
        $phone = $request->get('phone');
        $email = $request->get('email');
        $username = $request->get('username');
        $this->send_code($email, $key_code, $phone, $username);
    	return response()->json(['token_type' => 'Bearer ', 'token' => $token, 'status' => 'kode OTP terkirim'], 201);
    }

    public function login()
    {
    	$credentials = request()->only('username','password');

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

    public function change()
    {

    }

    public function forget()
    {
        Mail::send('emails.reset', ['username' => $username, 'key_code' => $key_code], function ($message) use($email)
        {
        $message->from('me@gmail.com', 'Support On-food');
        $message->to( $email );
        $message->subject('On-food Password Reset');
        });
    }

    public function send_code($email, $key_code, $phone, $username)
    {
        if ( !empty ( $phone ) ) {
            $sms = new Sms('hm0opd', 'Onfood');
            $sms->to($phone)
            ->text('Terimakasih '.$username.' telah menggunakan Aplikasi On-Food. Berikut Adalah kode konfirmasi untuk nomor anda '.$key_code.'.')
            ->send();
            //echo 'success';
            // Nexmo::message()->send([
            // 'to'   => '6285236938602',
            // 'from' => '6285815301508',
            // 'text' => 'Terimakasih, '.$username.' telah menggunakan produk kami. Berikut Adalah kode konfirmasi untuk nomor anda '.$key_code.'.'
            // ]);
        }else {
            Mail::send('emails.send', ['username' => $username, 'key_code' => $key_code], function ($message) use($email)
            {
            $message->from('me@gmail.com', 'Support On-food');
            $message->to( $email );
            $message->subject('On-food code confirmation');
            });
        }
    }
    public function confirm_code()
    {

    }

}