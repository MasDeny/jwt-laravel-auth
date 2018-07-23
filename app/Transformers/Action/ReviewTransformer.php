<?php

namespace App\Transformers\Action;

use App\Shop;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Shop $shop)
    {
        return [
            'create'        => route('review.create',$shop->id), 
            'updated'       => route('review.edit',$shop->id),
        ];
    }
}
