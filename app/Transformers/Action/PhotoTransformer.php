<?php

namespace App\Transformers\Action;

use App\Products_photos;
use League\Fractal\TransformerAbstract;

class PhotoTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Products_photos $photos)
    {
        return [
            'edited'        => route('photos.edit',$photos->id), 
            'deleted'       => route('photos.delete',$photos->id),
        ];
    }
}
