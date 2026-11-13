<x-layouts.app :title="'Roles'">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Roles
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Administración de roles del sistema.
            </p>
        </div>

        <a href="{{ route('roles.create') }}"
           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
            Crear rol
        </a>
    </div>

    @if (session('success'))
        <div class="p-3 mb-4 text-sm text-green-800 bg-green-100 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">

        <table class="w-full text-sm text-left text-gray-700">

            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre del rol
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Usuarios asociados
                    </th>

                    <th scope="col" class="px-6 py-3 text-right">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($roles as $role)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium text-gray-900">
                            @switch($role->name)
                                @case('admin')
                                    Administrador
                                    @break

                                @case('librarian')
                                    Bibliotecario
                                    @break

                                @case('member')
                                    Socio
                                    @break

                                @default
                                    {{ $role->name }}
                            @endswitch
                        </td>

                        <td class="px-6 py-4">
                            {{ $role->users_count }}
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if ((int) $role->id === (int) auth()->user()->role_id)
                                <span class="text-gray-500">Tu rol actual</span>
                            @else
                                @can('update', $role)
                                    <a href="{{ route('roles.edit', $role) }}"
                                    class="font-medium text-blue-600 hover:underline">
                                        Modificar
                                    </a>
                                @endcan

                                @if ($role->is_active)
                                    @can('deactivate', $role)
                                        <form
                                            action="{{ route('roles.deactivate', $role) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Está seguro de que desea desactivar este rol?')"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit">Desactivar</button>
                                        </form>
                                    @endcan
                                @else
                                    @can('activate', $role)
                                        <form
                                            action="{{ route('roles.activate', $role) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Está seguro de que desea activar este rol?')"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit">Activar</button>
                                        </form>
                                    @endcan
                                @endif
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            No hay roles registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>

</x-layouts.app>