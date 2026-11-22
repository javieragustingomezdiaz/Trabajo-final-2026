<?php

namespace App\Http\Requests;

use App\Models\Circulation;
use Illuminate\Foundation\Http\FormRequest;

class SearchCirculationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Circulation::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
            'fecha_hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_desde'],
            'socio' => ['nullable', 'string', 'max:100'],
            'titulo' => ['nullable', 'string', 'max:150'],
            'estado_id' => ['nullable', 'integer', 'exists:loan_statuses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_desde.date_format' => 'Los criterios de búsqueda no son válidos. Revisá la fecha desde.',
            'fecha_hasta.date_format' => 'Los criterios de búsqueda no son válidos. Revisá la fecha hasta.',
            'fecha_hasta.after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
            'socio.max' => 'Los criterios de búsqueda no son válidos. Revisá el nombre del socio.',
            'titulo.max' => 'Los criterios de búsqueda no son válidos. Revisá el título.',
            'estado_id.exists' => 'Los criterios de búsqueda no son válidos. Revisá el estado.',
        ];
    }

    public function filters(): array
    {
        $filters = $this->validated();

        foreach (['socio', 'titulo'] as $field) {
            $filters[$field] = isset($filters[$field]) ? trim($filters[$field]) : null;
        }

        return $filters;
    }

    public function hasCriteria(): bool
    {
        return collect($this->filters())
            ->contains(fn ($value) => $value !== null && $value !== '');
    }

    public function searchWasSubmitted(): bool
    {
        return collect(array_keys($this->rules()))
            ->contains(fn ($key) => $this->query->has($key));
    }
}
