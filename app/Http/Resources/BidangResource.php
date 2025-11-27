<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BidangResource extends JsonResource
{
    /**
     * @param  \App\Models\Bidang  $this->resource
     */
    public function toArray($request): array
    {
        return [
            'id'   => $this->id,
            'kode' => $this->kode,
            'nama' => $this->nama,
        ];
    }
}
