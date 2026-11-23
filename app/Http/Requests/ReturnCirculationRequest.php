<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnCirculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('recordReturn', $this->route('circulation')) ?? false;
    }

    public function rules(): array
    {
        return [
            'damaged' => ['sometimes', 'boolean'],
            'return_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
