<?php

namespace App\Transformers\Search;

use App\Products;
use League\Fractal\TransformerAbstract;

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
            'amount_rating' => $products->shop->review->count() < 1 ? 'Toko belum memiliki rating' : $products->shop->review->count(),
            'photo_product' => $products->products_photos === null ? url('/products/default.png') : url('/').'/products/'.$products->products_photos->filename,
            'details'       => route('product.show',$products->id),
        ];
    }
}
