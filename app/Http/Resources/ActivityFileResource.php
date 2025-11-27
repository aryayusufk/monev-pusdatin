<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityFileResource extends JsonResource
{
    /**
     * @param  \App\Models\ActivityFile  $this->resource
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'original_name'=> $this->original_name,
            'mime_type'    => $this->mime_type,
            'size'         => $this->size,
            'url'          => $this->path ? url('storage/'.$this->path) : null,
        ];
    }
}
