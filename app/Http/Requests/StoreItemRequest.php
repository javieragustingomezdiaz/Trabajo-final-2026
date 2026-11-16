<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Gate;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Item::class);
    }


    public function rules(): array
    {
        return [
            'item_status_id' => ['required', 'integer', 'exists:item_statuses,id'],
            'item_condition_id' => ['required', 'integer', 'exists:item_conditions,id'],
            'container_id' => ['required', 'integer', 'exists:containers,id'],
            'observation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_status_id.required' => 'El estado es obligatorio y no puede estar vacío.',
            'item_condition_id.required' => 'La condición es obligatoria y no puede estar vacía.',
            'container_id.required' => 'La ubicación es obligatoria y no puede estar vacía.',
            'observation.required' => 'La observación es obligatoria y no puede estar vacía.',
        ];
    }
}
