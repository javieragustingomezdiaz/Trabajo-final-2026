<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchReservationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAnyForStaff', Reservation::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
            'fecha_hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_desde'],
            'socio' => ['nullable', 'string', 'max:100'],
            'codigo_barras' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', Rule::in(['inmediata', 'cola'])],
            'alcance' => ['nullable', Rule::in(['activas', 'historial', 'todas'])],
            'estado_id' => ['nullable', 'integer', 'exists:reservation_statuses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_desde.date_format' => 'Los criterios de búsqueda ingresados no son válidos. Revisá las fechas.',
            'fecha_hasta.date_format' => 'Los criterios de búsqueda ingresados no son válidos. Revisá las fechas.',
            'fecha_hasta.after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
            'socio.max' => 'Los criterios de búsqueda ingresados no son válidos. Revisá el nombre del socio.',
            'codigo_barras.max' => 'Los criterios de búsqueda ingresados no son válidos. Revisá el código de barras.',
            'tipo.in' => 'Los criterios de búsqueda ingresados no son válidos. Revisá el tipo de reserva.',
            'alcance.in' => 'Seleccioná un alcance válido para la búsqueda.',
            'estado_id.exists' => 'Los criterios de búsqueda ingresados no son válidos. Revisá el estado.',
        ];
    }

    public function filters(): array
    {
        $filters = $this->validated();

        foreach (['socio', 'codigo_barras'] as $field) {
            $filters[$field] = isset($filters[$field]) ? trim($filters[$field]) : null;
        }

        return $filters;
    }

    public function hasCriteria(): bool
    {
        return collect($this->filters())
            ->contains(fn ($value) => $value !== null && $value !== '');
    }
}
