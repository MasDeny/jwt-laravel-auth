<?php

namespace App\Transformers\Search;

use App\Shop;
use League\Fractal\TransformerAbstract;

class ShopTransformer extends TransformerAbstract
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
            'address'       => $shop->address,
            'phone'         => $shop->user->phone,
            'description'   => $shop->description,
            'avatar'        => url('/').'/avatars/'.$shop->avatar,
            'rating'        => $shop->review->count() < 1 ? 'Toko belum memiliki rating' : 
                                round($shop->review->sum('rating')/$shop->review->count(),2),
            'details'       => route('profile.index',$shop->id),
        ];
    }
}
