<?php

namespace App\Transformers;

use App\Review;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Review $review)
    {
        return [
            'shop name'     => $review->shop->shop_name,
            'rating'        => $review->rating,
            'comment'       => $review->comment == null ? ' ' : $review->comment,
            'buyer'         => $review->profile->fullname,
        ];
    }
}
