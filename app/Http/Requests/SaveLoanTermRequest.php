<?php

namespace App\Http\Requests;

use App\Models\LoanTerm;
use Illuminate\Foundation\Http\FormRequest;

class SaveLoanTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', LoanTerm::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'bookable' => ['required', 'boolean'],
            'hours_on_standby' => ['nullable', 'required_if:bookable,1', 'integer', 'min:0'],
            'is_home_loanable' => ['required', 'boolean'],
            'home_loan_days' => ['nullable', 'required_if:is_home_loanable,1', 'integer', 'min:0'],
            'allow_in_library_use' => ['required', 'boolean'],
            'in_library_loan_hours' => ['nullable', 'required_if:allow_in_library_use,1', 'integer', 'min:0'],
            'is_renewable' => ['required', 'boolean'],
            'max_renewal' => ['nullable', 'required_if:is_renewable,1', 'integer', 'min:0'],
            'renewal_days' => ['nullable', 'required_if:is_renewable,1', 'integer', 'min:0'],
            'max_simultaneous_reservations' => ['nullable', 'integer', 'min:0'],
            'max_simultaneous_loans' => ['nullable', 'integer', 'min:0'],
            'sanctions_days_factor' => ['nullable', 'integer', 'min:0'],
            'damage_suspension_days' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.min' => 'El valor debe ser mayor o igual a 0.',
            '*.integer' => 'Ingresá un número entero válido.',
        ];
    }
}
