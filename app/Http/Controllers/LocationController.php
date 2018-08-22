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
            ['except' => ['warungs','create','update','mylocation']]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     //toUser(request('token'))
    public function create(Request $request)
    {
        $this->user = JWTAuth::toUser(request('token'));
        if (empty($this->user->shop->id)) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk mengakses ini'], 403);
        }
        if (!empty($this->user->shop->location) and $this->user->shop->location->count() > 0) {
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

    function warungs(){
        $lt = floatval(@$_GET['lt']);
        $lng = floatval(@$_GET['lng']);
        $data = [];
        foreach (Location::selectRaw('*,SQRT(POW('.$lt.'-`lat`,2)+POW('.$lng.'-`long`,2)*113.319) as ecludian')
                    ->orderBy('ecludian','ASC')->get() as $key => $v) {
            $data[] = [
                'ecludian' => $v->ecludian,
                'places'    => $v->name_location,
                'latitude'  => $v->lat,
                'longitude' => $v->long,
                'shop'      => [
                    'name'      => $v->shop->shop_name,
                    'owner'     => $v->shop->owner,
                    'address'   => $v->shop->address,
                    'more'      => route('profile.index',$v->shop_id)
                ],
            ];
        }
        return json_encode(['data'=>$data,'resp'=>$_GET]);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    function update(){
        $this->user = JWTAuth::toUser(request('token'));
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

    function myLocation(){
        try {
            $this->user = JWTAuth::toUser(request('token'));
            return fractal()
                ->item($this->user->shop->location)
                ->transformWith(new MapsTransformer)
                ->addMeta(['success'  => 'Lokasi telah diperbaharui'])
                ->toArray();
        } catch (Exception $e) {
            $e->getMessage();
            echo "string";
        }
    }

    
    // public function update(Request $request)
    // {
    //     $this->user = JWTAuth::parseToken()->authenticate();

    //     $location = $this->user->shop->location;
    //     $data = [
    //         'name_location' => request('place'),
    //         'lat'           => request('latitude'),
    //         'long'          => request('longitude'),
    //         ];

    //     $location->where('shop_id', $this->user->shop->id)->update($data);
    //     $location = $this->user->shop->location;
    //     return fractal()
    //         ->item($location)
    //         ->transformWith(new MapsTransformer)
    //         ->addMeta(['success'  => 'Lokasi telah diperbaharui'])
    //         ->toArray();
    // }
}
