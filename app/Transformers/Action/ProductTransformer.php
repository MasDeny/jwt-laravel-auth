<?php

namespace App\Transformers\Action;

use App\Products;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Products $products)
    {
        return [
            'edited'        => route('product.edit',$products->id), 
            'deleted'       => route('product.delete',$products->id),
        ];
    }
}
