<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Activity;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Activity|null $activity */
        $activity = $this->route('activity');

        if (!$activity) {
            return false;
        }

        // Cek policy: ActivityPolicy@update
        return $this->user()?->can('update', $activity) ?? false;
    }

    public function rules(): array
    {
        return [
            'kegiatan'  => ['sometimes','string','max:1000'],
            'tindak_lanjut' => ['sometimes','nullable','string','max:1000'],
            'kendala'       => ['sometimes','nullable','string','max:1000'],
            'percepatan'    => ['sometimes','nullable','string','max:1000'],
            'proyeksi_kedepan' => ['sometimes','nullable','string','max:1000'],

            //KALO INGIN MANUAL SOMETIMES GANTI JADI NULLABLE
            'occurred_at'   => ['nullable','date'],

            'iku_ids'   => ['sometimes','array','min:1'],
            'iku_ids.*' => ['integer','exists:ikus,id'],

            'contribution'   => ['nullable','array'],
            'contribution.*' => ['nullable','integer','min:0','max:100'],
        ];
    }
}
