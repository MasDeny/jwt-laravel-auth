<?php

namespace App\Transformers;

use App\Shop;
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
}
