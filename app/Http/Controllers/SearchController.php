<?php

namespace App\Http\Controllers;

use App\Products;
use App\Shop;
use App\Transformers\Search\ProductsTransformer;
use App\Transformers\Search\ShopTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function byPriceCheap()
    {
    	$products = Products::where('price', '<=', 25000)->orderBy('price', 'asc')->get();

    	return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    }

    public function byPriceExpensive()
    {
    	$products = Products::where('price', '>', 25000)->orderBy('price', 'asc')->get();

    	if (json_decode($products) == []) {
    		return response()->json(['success' => 'yahh , ga ada nih yang kamu cari']);
    	}
    	
    	return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    }

    public function byCategoryfood()
    {
    	$products = Products::where('product_type', 'food')->orderBy('product_name', 'asc')->get();

    	if (json_decode($products) == []) {
    		return response()->json(['success' => 'yahh , ga ada nih yang kamu cari']);
    	}
    	
    	return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    	
    }

    public function byCategorydrink()
    {
    	$products = Products::where('product_type','drink')->orderBy('product_name', 'asc')->get();

    	if (json_decode($products) == []) {
    		return response()->json(['success' => 'yahh , ga ada nih yang kamu cari']);
    	}
    	
    	return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    }

    public function byProduct(Request $request)
    {
    	$this->validate($request, [
            'search'     => 'required|min:1',
            ]);
    	$search = preg_split('/\s+/', $request->search);
    	$products = Products::where(function($query) use($search) {
    		foreach ($search as $value) {
    			$query->where('product_name', 'like', '%'.$value.'%');
    		}
    	})->orderBy('product_name', 'asc')->get();

    	if (json_decode($products) == []) {
    		return response()->json(['success' => 'yahh , ga ada nih yang kamu cari']);
    	}

    	return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    }

    public function byRating()
    {
    	$shop = Shop::with('review')->whereHas('review', function($query) {
    		$query->whereBetween('rating', ['4', '5']);
    	})->orderBy('shop_name', 'desc')->get();

    	if (json_decode($shop) == []) {
    		return response()->json(['success' => 'yahh , ga ada nih yang kamu cari']);
    	}

    	return fractal()
            ->collection($shop)
            ->transformWith(new ShopTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    }

    public function byShop(Request $request)
    {
    	$this->validate($request, [
            'search'     => 'required|min:1',
            ]);
    	$search = preg_split('/\s+/', $request->search);
    	$shop = Shop::where(function($query) use($search) {
    		foreach ($search as $value) {
    			$query->where('shop_name', 'like', '%'.$value.'%');
    		}
    	})->orderBy('shop_name', 'asc')->get();

    	if (json_decode($shop) == []) {
    		return response()->json(['success' => 'yahh , ga ada nih yang kamu cari']);
    	}

    	return fractal()
            ->collection($shop)
            ->transformWith(new ShopTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    	
    }

    public function byNewestShop()
    {
    	$shop = Shop::orderBy('created_at', 'asc')->paginate(5);
    	
    	return fractal()
            ->collection($shop)
            ->transformWith(new ShopTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    	
    }

    public function byNewestProduct()
    {
    	$products = Products::orderBy('created_at', 'asc')->paginate(5);
    	
    	return fractal()
            ->collection($products)
            ->transformWith(new ProductsTransformer)
            ->addMeta(['success' => 'ini nih produk yang kamu cari'])
            ->toArray();
    	
    }
}
