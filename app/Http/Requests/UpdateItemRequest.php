<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('item'));
    }

    public function rules(): array
    {
        return [
            'item_status_id' => ['required', 'integer', 'exists:item_statuses,id'],
            'item_condition_id' => ['required', 'integer', 'exists:item_conditions,id'],
            'container_id' => ['required', 'integer', 'exists:containers,id'],
            'observation' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_status_id.required' => 'El estado es obligatorio.',
            'item_status_id.exists' => 'El estado seleccionado no es válido.',
            'item_condition_id.required' => 'La condición es obligatoria.',
            'item_condition_id.exists' => 'La condición seleccionada no es válida.',
            'container_id.required' => 'La ubicación es obligatoria.',
            'container_id.exists' => 'El contenedor seleccionado no es válido.',
        ];
    }
}
