<?php

namespace App\Http\Requests;

use App\Models\Container;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class StoreContainerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Container::class);
    }

    public function rules(): array
    {
        $location = $this->route('location');

        return [
            'container_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('containers', 'name')
                    ->where(fn ($query) => $query->where('location_id', $location->id)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'container_name.required' => 'El nombre del contenedor es obligatorio.',
            'container_name.unique' => 'Ya existe un contenedor con ese nombre en esta ubicación.',
        ];
    }
}
