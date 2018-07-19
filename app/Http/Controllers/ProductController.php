<?php

namespace App\Http\Controllers;

use App\Products;
use App\Transformers\ProductsTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', 
            ['except' => ['show_by_id']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (empty($user->shop->id)) {
            return response()->json(['error' => 'Anda tidak diijinkan untuk mengakses ini'], 403);
        }
        $user_stat = $user->status_user;
        try {
            if ($user_stat == 2) {
            $this->validate($request, [
            'name'  => 'required|min:3',
            'type'  => 'required|in:food,drink',
            'price' => 'required|min:3|max:10',
            ]);

            $products = Products::create([
            'product_name'  => request('name'),
            'product_type'  => request('type'),
            'price'         => request('price'),
            'shop_id'       => $user->shop->id,
            ]);

            return fractal()
            ->item($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success'  => 'Produk Berhasil Ditambahkan'])
            ->toArray();
        }
        return response()->json(['error' => 'Pembuatan produk gagal, silahkan buat profil terlebih dahulu'], 202);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Penambahan profil gagal, periksa kembali koneksi anda'], 500);
        }
    }

    public function show()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $products = $user->shop->products;
        return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->includeAction()
            ->addMeta(['success'  => 'Berikut Produk yang anda miliki'])
            ->toArray();
        //return response()->json(['success' => 'Produk anda telah terhapus']);
    }

    public function show_by_id($id)
    {
        $product = Products::findOrFail($id);
        return fractal()
            ->item($product)
            ->transformWith(new ProductsTransformer)
            ->includeShop()
            ->toArray();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $products = $user->shop->products;

        $product = Products::findOrFail($id);
        
        $product->delete();
        
        return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success'  => 'Produk anda telah terhapus'])
            ->toArray();
        //return response()->json(['success' => 'Produk anda telah terhapus']);

    }
}
