<?php

namespace App\Http\Requests;

use App\Models\Circulation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCirculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Circulation::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:users,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'loan_type' => ['required', Rule::in([
                Circulation::TYPE_HOME,
                Circulation::TYPE_IN_LIBRARY,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required' => 'Seleccioná un socio.',
            'member_id.exists' => 'El socio seleccionado no existe.',
            'item_id.required' => 'Seleccioná un ejemplar.',
            'item_id.exists' => 'El ejemplar seleccionado no existe.',
            'loan_type.required' => 'Seleccioná el tipo de préstamo.',
            'loan_type.in' => 'El tipo de préstamo seleccionado no es válido.',
        ];
    }
}
