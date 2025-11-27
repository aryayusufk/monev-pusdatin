<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * @param  \App\Models\Activity  $this->resource
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,

            // Relasi
            'bidang'    => new BidangResource($this->whenLoaded('bidang')),
            'ikus'      => IkuResource::collection($this->whenLoaded('ikus')),
            'files'     => ActivityFileResource::collection($this->whenLoaded('files')),

            // Isi laporan
            'kegiatan'         => $this->kegiatan,
            'tindak_lanjut'    => $this->tindak_lanjut,
            'kendala'          => $this->kendala,
            'percepatan'       => $this->percepatan,
            'proyeksi_kedepan' => $this->proyeksi_kedepan,

            // Periodisasi
            'occurred_at' => optional($this->occurred_at)->toIso8601String(),
            'year'        => $this->year,
            'month'       => $this->month,
            'quarter'     => $this->quarter,
            'iso_week'    => $this->iso_week,

            // Seleksi triwulan
            'is_selected_quarterly'  => (bool) $this->is_selected_quarterly,
            'selected_quarterly_scope' => $this->selected_quarterly_scope,
            'selected_quarterly_at'    => optional($this->selected_quarterly_at)->toIso8601String(),
        ];
    }
}
