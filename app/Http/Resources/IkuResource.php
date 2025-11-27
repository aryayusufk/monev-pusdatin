<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IkuResource extends JsonResource
{
    /**
     * @param  \App\Models\Iku  $this->resource
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'kode'      => $this->kode,
            'nama'      => $this->nama,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
