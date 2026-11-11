<x-layouts.app :title="'Modificar rol'">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6">Modificar rol</h1>

        @php
            $selectedPermissions = old(
                'permissions',
                $role->permissions->pluck('id')->all()
            );
        @endphp

        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block font-medium mb-2">
                    Nombre del rol
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $role->name) }}"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                    required
                >

                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado del rol --}}
            <div class="mb-6">
                <label class="block font-medium mb-2">
                    Estado del rol
                </label>

                <p>
                    {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                </p>
            </div>

            <div class="mb-6">
                <h2 class="font-medium mb-3">Permisos</h2>

                @error('permissions')
                    <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
                @enderror

                @foreach ($permissions as $module => $modulePermissions)
                    <fieldset class="border rounded-md p-4 mb-4">
                        <legend class="font-medium px-2">
                            {{ __('modules.' . $module) }}
                        </legend>

                        <div class="space-y-2">
                            @foreach ($modulePermissions as $permission)
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        @checked(in_array(
                                            $permission->id,
                                            $selectedPermissions
                                        ))
                                    >

                                    <span>
                                        {{ __('permissions.' . $permission->name) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md"
                >
                    Guardar cambios
                </button>

                <a
                    href="{{ route('roles.index') }}"
                    class="px-4 py-2 border rounded-md"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>