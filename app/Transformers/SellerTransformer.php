<?php

namespace App\Transformers;

use App\Shop;
use App\Transformers\Action\ReviewTransformer;
use App\Transformers\MapsTransformer;
use App\Transformers\ProductsTransformer;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    protected $availableIncludes = [
        'products',
        'map',
        'review',
    ];

    public function transform(Shop $shop)
    {
        return [
            'shop_name'     => $shop->shop_name,
            'owner'         => $shop->owner,
            'phone'         => $shop->user->phone,
            'address'       => $shop->address,
            'description'   => $shop->description,
            'avatar'        => url('/').'/avatars/'.$shop->avatar,
            'rating'        => $shop->review->count() < 1 ? 'Toko belum memiliki rating' : 
                                round($shop->review->sum('rating')/$shop->review->count(),2),
            'comment'       => $shop->review->count() < 1 ? 'Toko belum memiliki rating' : 
                                route('review.show',$shop->id),
            'products'      => route('profile.products',$shop->id),
            'created at'    => $shop->created_at->diffForHumans(),
        ];
    }
    public function includeProducts(Shop $shop)
    {
        $products = $shop->products;
        return 
            $shop->products->count() < 1 ? 'Belum Memiliki Produk yang dijual' : 
                $this->collection($products, new ProductsTransformer);
    }

    public function includeMap(Shop $shop)
    {
        $map = $shop->location;
        return 
            $shop->location->count() < 1 ? 'Anda Belum Menentukan Posisi Saat ini' : 
                $this->item($map, new MapsTransformer);
    }

    public function includeReview(Shop $shop)
    {
        return $this->item($shop, new ReviewTransformer);
    }
}
