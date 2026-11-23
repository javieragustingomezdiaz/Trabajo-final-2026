<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewCirculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('renew', $this->route('circulation')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
