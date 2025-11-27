<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],

            // username unik, tapi boleh tetap sama dengan username user saat ini
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],

            // bidang (khusus role yang punya bidang)
            'bidang_id' => [
                'nullable', // superadmin boleh null
                'exists:bidangs,id',
            ],
        ];
    }
}
