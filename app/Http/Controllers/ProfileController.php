<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Shop;
use App\Transformers\BuyerTransformer;
use App\Transformers\SellerTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', 
            ['except' => ['index']]);
    }

    //menambahkan fungsi untuk membuat profile
    public function index(Shop $shop, $id)
    {
         $shop = $shop->find($id);
         return fractal()
            ->item($shop)
            ->transformWith(new SellerTransformer)
            ->includeProducts()
            ->includeMap()
            ->toArray();
    }

    public function create(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
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
            $this->user->update(['phone' => $request->phone, 'status_user' => 2 ]);

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
            $this->user->update(['phone' => $request->phone, 'status_user' => 2 ]);


            return fractal()
            ->item($profile)
            ->transformWith(new BuyerTransformer)
            ->addMeta(['success'  => 'Profil pembeli telah dibuat'], 201)
            ->toArray();
        }
        return response()->json(['error' => 'Pembuatan profil gagal, akun anda telah memiliki profil'], 202);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Pembuatan profil gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function update_profile(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $status = $this->user->status;
        if ($status === 'seller')
        {
            $data = [
            'shop_name'     => request('shop_name'),
            'owner'         => request('owner'),
            'address'       => request('address'),
            'description'   => request('description'),
            ];
            $table = 'shops';
            $transform = new SellerTransformer;
            $response = 'Profil penjual telah diperbaharui';
        }else{
            $data = [
                'fullname'  => request('fullname'),
                'sex'       => request('sex')
            ];
            $table = 'profiles';
            $transform = new BuyerTransformer;
            $response = 'Profil pembeli telah diperbaharui';
        }
        $this->user->update(['phone' => $request->phone]);
        DB::table($table)
            ->where('user_id', $this->user->id)
            ->update($data);
        if ($table == 'profiles') {
            $item = $this->user->profile;
        } else {
            $item = $this->user->shop;
        }
        return fractal()
            ->item($item)
            ->transformWith($transform)
            ->addMeta(['success'  => $response])
            ->toArray();
    }

    public function update_avatar(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        try {
        $this->validate($request,[
           'avatar'=>'required|mimes:jpeg,bmp,jpg,png,svg|between:1, 2048',
       ]);
        $status = $this->user->status;
        if ($request->hasFile('avatar')) {
        $imagename = $request->avatar->getClientOriginalName();
        $request->avatar->move(public_path("avatars"), $imagename);
            if ($status === 'seller')
            {
                $default = $this->user->shop->avatar;
                $table = 'shops';
            }else {
                $default = $this->user->profile->avatar;
                $table = 'profiles';
            }
            if ($default !== "default.jpg") {
                Storage::delete($default);
            }
            DB::table($table)
            ->where('user_id', $this->user->id)
            ->update(['avatar' => $imagename,]);

            return response()->json(['success'  => 'Update foto telah berhasil']);
        }
        return response()->json(['error'  => 'Foto tidak ditemukan'], 404);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Pembuatan profil gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function show(Shop $shop, Profile $profile)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $shop = $this->user->shop;
        $profile = $this->user->profile;
        if ($this->user->status_user == 0) {
            return response()->json(['error' => 'Tidak ditemukan, silahkan isi profile anda'], 404);
        }
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
}
