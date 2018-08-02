<?php

namespace App\Transformers;

use App\Products_photos;
use App\Transformers\Action\PhotoTransformer;
use League\Fractal\TransformerAbstract;

class PhotosTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'action',
    ];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Products_photos $photos)
    {
        return [
            'filename'      => $photos->original_filename,
            'url'           => url('/').'/products/'.$photos->filename,
            'detail'        => route('photos.show',$photos->id),
            'all photos'    => route('photos.index',$photos->products_id),
        ];
    }

    public function includeAction(Products_photos $photos)
    {
        return $this->item($photos, new PhotoTransformer);
    }
}
