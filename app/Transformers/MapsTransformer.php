<?php

namespace App\Transformers;

use App\Location;
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
            'id'        => $location->id,
            'places'    => $location->name_location,
            'latitude'  => $location->lat,
            'longitude' => $location->long,
        ];
    }
}
