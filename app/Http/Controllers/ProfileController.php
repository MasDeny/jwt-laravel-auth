<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Shop;
use App\Transformers\BuyerTransformer;
use App\Transformers\SellerTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    //menambahkan fungsi untuk membuat profile
    public function create(Request $request)
    {
        $status = $this->user->status;
        $user_stat = $this->user->status_user;
        try {
            if ($user_stat == 1) {
            if ($status === 'seller') {
            $this->validate($request, [
            'shop_name'     => 'required|min:3',
            'owner'         => 'required|min:3',
            'phone'         => 'required|min:11|max:14',
            'address'       => 'required|min:6',
            'description'   => 'min:5',
            ]);

            $shop = Shop::create([
            'shop_name'     => request('shop_name'),
            'owner'         => request('owner'),
            'address'       => request('address'),
            'description'   => request('description'),
            'user_id'       => $this->user->id,
            ]);
            $this->user->update(['phone' => $request->phone, 'status_user' => 3 ]);

            return fractal()
            ->item($shop)
            ->transformWith(new SellerTransformer)
            ->addMeta(['success'  => 'Profile toko telah dibuat'])
            ->toArray();
            }

            //validasi profile
            $this->validate($request, [
            'fullname'    => 'required|min:5',
            'sex'         => 'required|in:male,female',
            'phone'       => 'min:11|max:14',
            ]);

            $profile = Profile::create([
            'fullname'  => request('fullname'),
            'sex'       => request('sex'),
            'user_id'   => $this->user->id,
            ]);
            $this->user->update(['phone' => $request->phone, 'status_user' => 3 ]);


            return fractal()
            ->item($profile)
            ->transformWith(new BuyerTransformer)
            ->addMeta(['success'  => 'Profil pembeli telah dibuat'], 201)
            ->toArray();
        }
        return response()->json(['error' => 'Pembuatan profil gagal, akun anda telah memiliki profil'], 500);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Pembuatan profil gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function update_profile(Request $request)
    {
        $status = $this->user->status;
        if ($status === 'seller')
        {
           $data = [
            'shop_name'     => request('shop_name'),
            'owner'         => request('owner'),
            'address'       => request('address'),
            'description'   => request('description'),
            ];
            $table = shops;
        }else{
            $data = [
                'fullname'  => request('fullname'),
                'sex'       => request('sex')
            ];
            $table = profiles;
        }
        $this->user->update(['phone' => $request->phone]);
        DB::table($table)
            ->where('user_id', $this->user->id)
            ->update($data);

    }

    public function update_avatar(Request $request)
    {
        try {
        $this->validate($request,[
           'avatar'=>'required|mimes:jpeg,bmp,jpg,png,svg|between:1, 2048',
       ]);
        $status = $this->user->status;
        if ($request->hasFile('avatar')) {
        $imagename = $request->avatar->getClientOriginalName();
        $avatar = $request->avatar->storeAs('avatars', 'avatar_'.$imagename);
            if ($status === 'seller')
            {
                $default = $this->user->shop->avatar;
                $table = shops;
            }else {
                $default = $this->user->profile->avatar;
                $table = profiles;
            }
            if (!$default==='avatars/default.jpg') {
                Storage::delete($default);
            }
            DB::table($table)
            ->where('user_id', $this->user->id)
            ->update(['avatar' => $avatar,]);

            return response()->json(['success'  => 'Update foto telah berhasil']);
        }
        return response()->json(['error'  => 'Foto tidak ditemukan'], 404);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Pembuatan profil gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function show(Shop $shop, Profile $profile)
    {
        $shop = $this->user->shop;
        $profile = $this->user->profile;
        try {
        $status = $this->user->status;
        if ($status === 'seller')
        {
            return fractal()
            ->item($shop)
            ->transformWith(new SellerTransformer)
            ->toArray();
        }
        return fractal()
            ->item($profile)
            ->transformWith(new BuyerTransformer)
            ->toArray();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Load data gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function destroy()
    {

    }
}
