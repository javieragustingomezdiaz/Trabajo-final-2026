<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Location;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Location::class);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la ubicación es obligatorio.',
            'name.unique' => 'Ya existe una ubicación con ese nombre.',
        ];
    }
}
