<?php

namespace App\Http\Requests;

use App\Models\GlobalRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGlobalRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', GlobalRule::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'max_simultaneous_reservations' => ['required', 'integer', 'min:0'],
            'reservation_offer_hours' => ['required', 'integer', 'min:1'],
            'max_simultaneous_loans' => ['required', 'integer', 'min:0'],
            'max_loan_days' => ['required', 'integer', 'min:0'],
            'max_consultation_hours' => ['required', 'integer', 'min:0'],
            'grace_hours' => ['required', 'integer', 'min:0'],
            'max_sanction_days' => ['required', 'integer', 'min:0'],
            'max_renewals' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'Este campo es obligatorio.',
            '*.integer' => 'Ingresá un número entero válido.',
            '*.min' => 'El valor debe ser mayor o igual a :min.',
        ];
    }
}
