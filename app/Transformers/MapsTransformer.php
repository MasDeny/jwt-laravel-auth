<?php

namespace App\Transformers;

use App\Location;
use App\Transformers\SellerTransformer;
use League\Fractal\TransformerAbstract;

class MapsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    public function transform(Location $location)
    {
        return [
            'places'    => $location->name_location,
            'latitude'  => $location->lat,
            'longitude' => $location->long,
            'shop'      => [
                'name'      => $location->shop->shop_name,
                'owner'     => $location->shop->owner,
                'address'   => $location->shop->address,
                'more'      => route('profile.index',$location->shop_id)
            ],
        ];
    }
}
