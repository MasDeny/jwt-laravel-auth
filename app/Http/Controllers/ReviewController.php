<?php

namespace App\Http\Controllers;

use App\Review;
use App\Transformers\ReviewTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', 
            ['except' => ['show']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->status === "seller") {
            return response()->json(['error' => 'Hanya pembeli yang diperbolehkan untuk memberikan rating'], 403);
        }
        $user_stat = $user->status_user;
        try {
            if ($user_stat == 2) {
                if ($user->profile->review->where('shop_id',$id)->count() > 0) {
                    return response()->json(['error' => 'Hanya satu review pada satu toko'], 406);
            }
            $this->validate($request, [
            'rating'    => 'required',
            ]);

            $review = Review::create([
            'comment'       => request('comment'),
            'rating'        => request('rating'),
            'profile_id'    => $user->profile->id,
            'shop_id'       => $id,
            ]);

            return fractal()
            ->item($review)
            ->transformWith(new ReviewTransformer)
            ->addMeta(['success'  => 'Review Berhasil Ditambahkan'])
            ->toArray();
        }
        return response()->json(['error' => 'Pembuatan produk gagal, silahkan buat profil terlebih dahulu'], 202);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Penambahan review gagal, periksa kembali koneksi anda'], 500);
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
        $review = Review::where('shop_id', $id)->get();
        return fractal()
            ->collection($review)
            ->transformWith(new ReviewTransformer)
            ->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $data = [
            'product_name'  => request('name'),
            'product_type'  => request('type'),
            'price'         => request('price'),
            ];
        DB::table('products')->where('id', $id)->update($data);
        $products = $user->shop->products;
        $product = $products->where('id', $id);

        return fractal()
            ->collection($product)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success'  => 'Produk telah diperbaharui'])
            ->toArray();
    }
}
