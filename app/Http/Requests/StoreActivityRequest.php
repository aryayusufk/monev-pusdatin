<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pakai policy ActivityPolicy::create
        return $this->user()?->can('create', Activity::class) ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'kegiatan'  => ['required','string','max:1000'],
            'tindak_lanjut' => ['nullable','string','max:1000'],
            'kendala'       => ['nullable','string','max:1000'],
            'percepatan'    => ['nullable','string','max:1000'],
            'proyeksi_kedepan' => ['nullable','string','max:1000'],

            //KALO INGIN JADI MANUAL HAPUS OCCURED_AT
            //'occurred_at'   => ['required','date'],

            'iku_ids'   => ['required','array','min:1'],
            'iku_ids.*' => ['integer','exists:ikus,id'],

            'contribution'   => ['nullable','array'],
            'contribution.*' => ['nullable','integer','min:0','max:100'],

            'file_upload' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];

        // HANYA role pusat yang wajib pilih bidang manual.
        // user_bidang & monev_bidang TIDAK wajib kirim bidang_id,
        // karena nanti di controller bidang_id di-set otomatis dari user.
        if (! $this->user()?->hasRole('user_bidang') && ! $this->user()?->hasRole('monev_bidang')) {
            $rules['bidang_id'] = ['required','exists:bidangs,id'];
        }

        return $rules;
    }
}
