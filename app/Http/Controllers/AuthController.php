<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use JWTAuth;
use Keygen\Keygen;
use Nasution\ZenzivaSms\Client as Sms;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    //Fungsi untuk mendaftar pada aplikasi
    public function register(Request $request)
    {
    	$this->validate($request, [
    		'username'    => 'required|unique:users,username',
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
    	return response()->json(['token_type' => 'Bearer ', 'token' => $token, 'success' => 'kode OTP terkirim'], 201);
    }

    public function send_code($email, $key_code, $phone, $username)
    {
        try {
        if ( !empty ( $phone ) ) {
            $sms = new Sms('hm0opd', 'Onfood');
            $sms->to($phone)
            ->text('Terimakasih '.$username.' telah menggunakan Aplikasi On-Food. Berikut Adalah kode konfirmasi untuk nomor anda '.$key_code.'.')
            ->send();
        }else {
            Mail::send('emails.send', ['username' => $username, 'key_code' => $key_code], function ($message) use($email)
            {
            $message->from('me@gmail.com', 'Support On-food');
            $message->to( $email );
            $message->subject('On-food code confirmation');
            });
        }
    } catch (JWTException $e) {
        return response()->json(['error' => 'proses gagal, periksa kembali koneksi anda'], 500);
    }
    }

    public function confirm_code(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|min:5',
        ]);
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($request->code !== $user->code) {
                return response()->json(['error' => 'Kode konfirmasi salah !'], 500);
            } else {
                //mengganti status user dari 0 ke 1 (jika akun berhasil terdaftar)
                $user->status_user = '1';
                $user->save();
                return fractal()
                ->item($user)
                ->transformWith(new UserTransformer)
                ->addMeta([
                    'success'  => 'Kode Konfirmasi sesuai'
                ])
                ->toArray();
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'register gagal, periksa kembali koneksi anda'], 500);
        }

    }

    //fungsi untuk login pada aplikasi
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
        $user = JWTAuth::toUser($token);
    	return fractal()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->addMeta([
                'token_type'    => 'Bearer ',
                'token'         => $token,
                'success'       => 'Berhasil Login'
            ])
            ->toArray();

    }

    // ! fungsi untuk mengganti password pada aplikasi
    public function change(Request $request)
    {
        try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
        return response()->json(['user tidak ditemukan'], 404);
        }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
        return response()->json(['token_absent'], $e->getStatusCode());
        }
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        $oldPassword    = $request->old_password;
        $newPassword    = $request->new_password;
        if (!(Hash::check($oldPassword, $user->password))) {
            // The passwords matches
        return response()->json(['error' => 'Password lama anda salah. Silahkan ulangi lagi'], 500);
        }

        if(strcmp($oldPassword, $newPassword) == 0){
            //Current password and new password are same
            return response()->json(['error' => 'Password baru tidak boleh sama dengan password lama. Silahkan ganti dengan password yang lain'], 500);
        }
        //dd('hell yeah bitch !');
        $user->password = bcrypt($newPassword);
        $user->save();
       return response()->json(['success' => 'Password telah berhasil diganti !'], 200);
    }

    //fungsi untuk reset password melalui email
    public function reset_password(Request $request)
    {
        $this->validate($request, [
            'email'       => 'required',
        ]);
        $email = $request->email;
        $new_password = $request->new_password;
        $email_validate = !!User::where('email', $email)->first();
        if (!$email_validate) {
            return response()->json(['error' => 'Kami tidak dapat menemukan akun dengan email '.$email], 404);
        } else {
            $key_code = Keygen::numeric(5)->generate();
            $this->send_reset($email, $key_code);
            return response()->json(['success' => 'Reset password terkirim, silahkan cek email anda']);
        }
    }

    public function send_reset($email, $key_code)
    {
        try {
        Mail::send('emails.reset', ['key_code' => $key_code], function ($message) use($email)
        {
        $message->from('me@gmail.com', 'Support On-food');
        $message->to( $email );
        $message->subject('On-food Password Reset');
        });
        DB::table('users')
            ->where('email', $email)
            ->update(['status_user' => 2, 'code' => $key_code, ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'proses gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function reset_confirm(Request $request)
    {
        try{
        $this->validate($request, [
            'new_code'       => 'required',
            'new_password'    => 'required|min:6|confirmed',
        ]);
        $new_code = $request->new_code;
        $new_password = $request->new_password;
        $code_validate = !!User::where('code', $new_code)->first();
        if (!$code_validate) {
            return response()->json(['error' => 'kode konfirmasi yang anda masukkan salah, silahkan isi kode konfirmasi secara valid', 'status' => 'false'], 404);
        } else {
            DB::table('users')
            ->where('code', $new_code)
            ->update(['status_user' => 1, 'password' => bcrypt($new_password), ]);
            return response()->json(['success' => 'Password telah di ubah, silahkan lakukan login ulang']);
        }
        } catch (Exception $e) {
            return response()->json(['error' => 'proses gagal, periksa kembali koneksi anda'], 500);
        }
    }

}