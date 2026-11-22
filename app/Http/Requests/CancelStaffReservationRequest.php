<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class CancelStaffReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cancelForMember', $this->route('reservation')) ?? false;
    }

    public function rules(): array
    {
        return [
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancellation_reason.required' => 'Ingresá la justificación de la cancelación.',
            'cancellation_reason.max' => 'La justificación no puede superar los 500 caracteres.',
        ];
    }
}
