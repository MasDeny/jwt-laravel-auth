<?php

namespace App\Transformers\Action;

use App\Location;
use League\Fractal\TransformerAbstract;

class MapTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Location $location)
    {
        return [
            'name'      => $location->shop->shop_name,
            'owner'     => $location->shop->owner,
            'address'   => $location->shop->address,
            'more'      => route('profile.index',$location->shop_id)
        ];
    }
}
