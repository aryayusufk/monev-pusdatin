<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportExportResource extends JsonResource
{
    /**
     * @param  \App\Models\ReportExport  $this->resource
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'period_type' => $this->period_type,
            'year'        => $this->year,
            'month'       => $this->month,
            'quarter'     => $this->quarter,
            'iso_week'    => $this->iso_week,

            'scope'       => $this->scope,
            'bidang_id'   => $this->bidang_id,

            'template_id' => $this->template_id,
            'status'      => $this->status,

            'file_url'    => $this->file_path ? url('storage/'.$this->file_path) : null,

            'created_at'  => optional($this->created_at)->toIso8601String(),
        ];
    }
}
