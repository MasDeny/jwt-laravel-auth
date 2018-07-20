<?php

namespace App\Transformers;

use App\Location;
use App\Transformers\Action\MapTransformer;
use League\Fractal\TransformerAbstract;

class MapsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $availableIncludes = [
        'detail',
    ];

    public function transform(Location $location)
    {
        return [
            'places'    => $location->name_location,
            'latitude'  => $location->lat,
            'longitude' => $location->long,
        ];
    }

    public function includeDetail(Location $location)
    {
        return $this->item($location, new MapTransformer);
    }
}
