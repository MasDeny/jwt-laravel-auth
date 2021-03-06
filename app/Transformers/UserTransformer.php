<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'username'      => $user->username,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'login_as'      => $user->status,
            'status'        => $user->status_user,
            'registered'    => $user->created_at->diffForHumans(),
        ];
    }
}
