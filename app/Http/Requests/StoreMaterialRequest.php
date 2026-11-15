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
            'author_ids' => ['sometimes', 'array'],
            'author_ids.*' => ['integer', 'distinct', 'exists:authors,id'],

            'topic_ids' => ['sometimes', 'array'],
            'topic_ids.*' => ['integer', 'distinct', 'exists:topics,id'],

            'new_authors' => ['sometimes', 'array', 'max:50'],
            'new_authors.*' => ['required', 'array'],
            'new_authors.*.name' => ['required', 'string', 'max:255'],
            'new_authors.*.author_type_id' => ['required', 'integer', 'exists:author_types,id'],
            'new_authors.*.birth_date' => ['nullable', 'date'],
            'new_authors.*.death_date' => [
                'nullable',
                'date',
                'after_or_equal:new_authors.*.birth_date',
            ],

            'new_topics' => ['sometimes', 'array', 'max:100'],
            'new_topics.*' => ['required', 'array'],
            'new_topics.*.name' => ['required', 'string', 'max:255'],
        ];

        return array_merge($baseRules, $specialization['rules']);
    }
}
