<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Material;
use App\Models\MaterialType;
use App\Services\MaterialSpecializationRegistry;
use Illuminate\Support\Facades\Gate;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Material::class);
    }

    public function rules(MaterialSpecializationRegistry $registry): array
    {
        /** @var MaterialType $materialType */
        $materialType = $this->route('materialType');

        $specialization = $registry->forType($materialType);

        $baseRules = [
            'title' => ['required', 'string', 'max:255'],
            'edition' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'date'],
            'identifier_value' => ['nullable', 'string', 'max:255'],
            'dimension' => ['nullable', 'string', 'max:255'],
            'extent' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover' => ['nullable', 'string', 'max:255'],

            'publisher_id' => ['required', 'integer', 'exists:publishers,id'],
            'identifier_type_id' => ['required', 'integer', 'exists:identifier_types,id'],
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'media_type_id' => ['required', 'integer', 'exists:media_types,id'],
            'content_type_id' => ['required', 'integer', 'exists:content_types,id'],
            'carrier_type_id' => ['required', 'integer', 'exists:carrier_types,id'],
        ];

        return array_merge($baseRules, $specialization['rules']);
    }
}
