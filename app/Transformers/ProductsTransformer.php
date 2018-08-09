<?php

namespace App\Transformers;

use App\Products;
use App\Transformers\Action\ProductTransformer;
use App\Transformers\PhotosTransformer;
use App\Transformers\SellerTransformer;
use League\Fractal\TransformerAbstract;

class ProductsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    protected $availableIncludes = [
        'shop',
        'action',
        'photos',
    ];

    public function transform(Products $products)
    {
        return [
            'product_name'  => $products->product_name,
            'product_type'  => $products->product_type,
            'price'         => $products->price,
            'photo'         => $products->products_photos === null ? url('/products/default.png') : url('/').'/products/'.$products->products_photos->filename,
            'detail'        => route('product.show',$products->id)
        ];
    }

    public function includeAction(Products $products)
    {
        return $this->item($products, new ProductTransformer);
    }

    public function includePhotos($products)
    {
        $photos = $products->products_photos->where('products_id',$products->id)->get();
        return $this->collection($photos, new PhotosTransformer);
    }

    public function includeShop(Products $products)
    {
        $shop = $products->shop;
        return $this->item($shop, new SellerTransformer);
    }
}
