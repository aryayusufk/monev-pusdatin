<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRunningTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dibatasi di route pakai middleware can:
    }

    public function rules(): array
    {
        return [
            'enabled' => ['required', 'boolean'],
            'content' => ['nullable', 'string', 'max:500'],
        ];
    }
}
