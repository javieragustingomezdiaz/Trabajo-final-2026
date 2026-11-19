<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreStaffReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('createForMember', Reservation::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:users,id'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required' => 'Seleccioná un socio.',
            'member_id.exists' => 'El socio seleccionado no existe.',
            'material_id.required' => 'Seleccioná un material.',
            'material_id.exists' => 'El material seleccionado no existe.',
        ];
    }
}