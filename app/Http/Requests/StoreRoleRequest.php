<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max: 50', 'unique:roles,name'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [
                'required', 
                'integer', 
                'distinct', 
                'exists:permissions,id',
                Rule::exists('permissions', 'id')
                    ->whereNotIn('name', [
                        'roles.view',
                        'roles.create',
                        'roles.update',
                        'roles.deactivate',
                        'permissions.view',
                        'permissions.assign',
                    ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de rol es obligatorio.',
            'name.string' => 'El nombre de rol debe ser un texto.',
            'name.max' => 'El nombre de rol no puede superar los 50 caracteres.',
            'name.unique' => 'Ya existe un rol con ese nombre.',

            'permissions.required' => 'El rol no puede ser creado sin privilegios mínimos de acceso.',
            'permissions.array' => 'La selección de permisos no es válida.',
            'permissions.min' => 'El rol no puede ser creado sin privilegios mínimos de acceso.',
            'permissions.*.required' => 'La selección de permisos no es válida.',
            'permissions.*.integer' => 'La selección de permisos no es válida.',
            'permissions.*.distinct' => 'No se puede seleccionar un permiso más de una vez.',
            'permissions.*.exists' => 'Uno de los permisos seleccionados no existe.',
        ];
    }
}
