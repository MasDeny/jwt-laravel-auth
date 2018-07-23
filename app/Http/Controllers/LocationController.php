<?php

namespace App\Http\Controllers;

use App\Location;
use App\Transformers\MapsTransformer;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', 
            ['except' => ['show','index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $maps = Location::get();
        return fractal()
            ->collection($maps)
            ->transformWith(new MapsTransformer)
            ->includeDetail()
            ->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        if (empty($this->user->shop->id)) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk mengakses ini'], 403);
        }
        if ($this->user->shop->location->count() > 1) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk menambahkan lokasi lebih dari 1'], 409);
        }
        $user_stat = $this->user->status_user;
        try {
            if ($user_stat == 2) {
            $this->validate($request, [
            'place'     => 'min:3',
            'latitude'  => 'required|min:3',
            'longitude' => 'required|min:9',
            ]);

            $maps = Location::create([
            'name_location' => request('place'),
            'lat'           => request('latitude'),
            'long'          => request('longitude'),
            'shop_id'       => $this->user->shop->id,
            ]);

            return fractal()
            ->item($maps)
            ->transformWith(new MapsTransformer)
            ->addMeta(['success'  => 'Lokasi telah ditentukan'])
            ->toArray();
        }
        return response()->json(['error' => 'Penentuan Lokasi gagal, silahkan buat profil terlebih dahulu'], 202);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Pembuatan profil gagal, periksa kembali koneksi anda'], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $maps = Location::findOrFail($id);
        return fractal()
            ->item($maps)
            ->transformWith(new MapsTransformer)
            ->includeDetail()
            ->toArray();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $location = $this->user->shop->location;
        $data = [
            'name_location' => request('place'),
            'lat'           => request('latitude'),
            'long'          => request('longitude'),
            ];

        $location->where('shop_id', $this->user->shop->id)->update($data);
        $location = $this->user->shop->location;
        return fractal()
            ->item($location)
            ->transformWith(new MapsTransformer)
            ->addMeta(['success'  => 'Lokasi telah diperbaharui'])
            ->toArray();
    }
}
