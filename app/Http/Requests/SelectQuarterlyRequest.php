<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;

class SelectQuarterlyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Activity $activity */
        $activity = $this->route('activity');

        // Pakai ActivityPolicy::selectQuarterly
        return $this->user()?->can('selectQuarterly', $activity) ?? false;
    }

    public function rules(): array
    {
        return [
            // true = pilih jadi laporan triwulan
            // false = batalkan dari laporan triwulan
            'select' => ['required','boolean'],
        ];
    }
}
