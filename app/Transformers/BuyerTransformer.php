<?php

namespace App\Transformers;

use App\Profile;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
   public function transform(Profile $profile)
    {
        return [
            'fullname'      => $profile->fullname,
            'sex'           => $profile->sex,
            'avatar'        => url('/').'/avatars/'.$profile->avatar,
            'phone'         => $profile->user->phone,
            'created at'    => $profile->created_at->diffForHumans(),
        ];
    }
}
