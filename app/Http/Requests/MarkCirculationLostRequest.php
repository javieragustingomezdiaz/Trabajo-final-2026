<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkCirculationLostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('markLost', $this->route('circulation')) ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Ingresá una justificación para marcar el préstamo como perdido.',
            'reason.max' => 'La justificación no puede superar los 500 caracteres.',
        ];
    }
}
