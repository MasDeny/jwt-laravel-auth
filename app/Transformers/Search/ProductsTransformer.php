<?php

namespace App\Transformers\Search;

use League\Fractal\TransformerAbstract;
use App\Products;

class ProductsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    public function transform(Products $products)
    {
        return [
            'shop_name'     => $products->shop->shop_name,
            'product_name'  => $products->product_name,
            'product_type'  => $products->product_type,
            'price'         => $products->price,
            'shop_rating'   => $products->shop->review->count() < 1 ? 'Toko belum memiliki rating' : round($products->shop->review->sum('rating')/$products->shop->review->count(),2),

            // 'shop_photos'   => $products->shop->review->count() < 1 ? 'Toko belum memiliki foto produk' : $products->products_photos->where('product_id',$products->id)->first(),
            
            'details'       => route('product.show',$products->id),
        ];
    }

}
