<?php

namespace App\Http\Controllers;

use App\Products_photos;
use App\Transformers\PhotosTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class PhotosController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', 
            ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $photos = Products_photos::where('products_id', $id)->get();

        return fractal()
                ->collection($photos)
                ->transformWith(new PhotosTransformer)
                ->addMeta(['success'  => 'Berikut daftar foto pada produk ini'])
                ->toArray();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (empty($user->shop->id)) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk mengakses ini'], 403);
        }

        if ($user->shop->products->find($id) === null) {
            return response()->json(['error' => 'Produk yang anda akses bukan milik anda'], 403);
        }

        $user_stat = $user->status_user;
        try {
            if ($user_stat == 2) {
                $this->validate($request, [
                'upload'  => 'required|mimes:jpeg,jpg,png|max:500000',
                ]);

                if (!empty(Products_photos::where('products_id', $id)) and Products_photos::where('products_id', $id)->count() > 3) {
                return response()->json(['success' => 'Batas maksimal foto hanya 4']);
                }

                if ($request->hasFile('upload')) {
                $shop = $user->shop->shop_name;

                $imagename = 'Product'.'_'.time().'_'.$shop.'_'. preg_replace('/\s+/','_',$request->upload->getClientOriginalName());

                $path = $request->upload->storeAs(preg_replace('/\s+/','','products/'.$shop),$imagename);
                $mime = $request->file('upload')->getMimeType();

                $img = Image::make($request->upload->move(public_path(preg_replace('/\s+/','',"products/$shop")), $path));

                $img->resize(800, null, function ($constraint) {
                     $constraint->aspectRatio();
                     $constraint->upsize();
                });
                $img->save();
                $photos = Products_photos::create([
                    'filename'          => $path,
                    'mime'              => $mime,
                    'original_filename' => $request->upload->getClientOriginalName(),
                    'products_id'        => $id,
                ]);

                return fractal()
                ->item($photos)
                ->transformWith(new PhotosTransformer)
                ->addMeta(['success'  => 'Foto Produk Berhasil Ditambahkan'])
                ->toArray();
                }
            }
        return response()->json(['error' => 'Penambahan foto produk gagal, silahkan tambahkan produk terlebih dahulu'], 202);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Penambahan foto produk gagal, periksa kembali koneksi anda'], 500);
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
        $user = JWTAuth::parseToken()->authenticate();
        $photos = Products_photos::where('id', $id)->get();
        if ($user->status === 'buyer') {
            return fractal()
                ->collection($photos)
                ->transformWith(new PhotosTransformer)
                ->toArray();
        } else {
            
            $file = Products_photos::findOrFail($id);
            if ($user->shop->products->find($file->products_id) === null) {
            return fractal()
                ->collection($photos)
                ->transformWith(new PhotosTransformer)
                ->toArray();
            }

            return fractal()
                ->collection($photos)
                ->transformWith(new PhotosTransformer)
                ->includeAction()
                ->toArray();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $file = Products_photos::findOrFail($id);
        if (empty($user->shop->id)) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk mengakses ini'], 403);
        }

        if ($user->shop->products->find($file->products_id) === null) {
            return response()->json(['error' => 'Foto produk yang anda akses bukan milik anda'], 403);
        }
        if ($request->hasFile('upload')) {
            $shop = $user->shop->shop_name;

            $imagename = 'Product'.'_'.time().'_'.$shop.'_'. preg_replace('/\s+/','_',$request->upload->getClientOriginalName());

                $path = $request->upload->storeAs(preg_replace('/\s+/','','products/'.$shop),$imagename);
                $mime = $request->file('upload')->getMimeType();

                $img = Image::make($request->upload->move(public_path(preg_replace('/\s+/','',"products/$shop")), $path));

                $img->resize(800, null, function ($constraint) {
                     $constraint->aspectRatio();
                     $constraint->upsize();
                });

                $img->save();

                if ($file->filename !== "default.jpg") {
                    Storage::delete('/products/'.$file->filename);
                }
        $data = [
            'filename'          => $path,
            'mime'              => $mime,
            'original_filename' => $request->upload->getClientOriginalName(),
            ];
        DB::table('products_photos')->where('id', $id)->update($data);

        return response()->json(['success'  => 'Foto produk telah diperbaharui']);
    }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $file = Products_photos::findOrFail($id);
        if (empty($user->shop->id)) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk mengakses ini'], 403);
        }

        if ($user->shop->products->find($file->products_id) === null) {
            return response()->json(['error' => 'Foto produk yang anda akses bukan milik anda'], 403);
        }

        $photos = Products_photos::findOrFail($id);
        Storage::delete('/products/'.$file->filename);
        $photos->delete();
        
        return response()->json(['success'  => 'Produk anda telah terhapus']);
    }
}
