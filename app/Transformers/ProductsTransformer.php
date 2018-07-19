<?php

namespace App\Transformers;

use App\Products;
use App\Transformers\SellerTransformer;
use App\Transformers\Action\ProductTransformer;
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
    ];

    public function transform(Products $products)
    {
        return [
            'product_name'  => $products->product_name,
            'product_type'  => $products->product_type,
            'price'         => $products->price,
        ];
    }

    public function includeAction(Products $products)
    {
        return $this->item($products, new ProductTransformer);
    }

    public function includeShop(Products $products)
    {
        $shop = $products->shop;
        return $this->item($shop, new SellerTransformer);
    }
}
