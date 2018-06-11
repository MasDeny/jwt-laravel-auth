<?php

namespace App\Transformers;

use App\Shop;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
   public function transform(Shop $shop)
    {
        return [
            'shop_name'     => $shop->shop_name,
            'owner'         => $shop->owner,
            'phone'         => $shop->user->phone,
            'address'       => $shop->address,
            'description'   => $shop->description,
            'avatar'        => url('/').'/images/'.$shop->avatar,
            'created at'    => $shop->created_at->diffForHumans(),
        ];
    }
}
