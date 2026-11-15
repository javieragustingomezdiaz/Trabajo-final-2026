<x-layouts.app title="Buscar material">
    <section class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Buscar material</h1>
                <p class="mt-2 text-slate-600">
                    Búsqueda interna del catálogo para bibliotecarios y administradores.
                </p>
            </div>

            <a href="{{ route('materials.create') }}"
               class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Registrar material
            </a>
        </div>

        @if (session('success'))
            <p class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </p>
        @endif

        <form method="GET" action="{{ route('materials.search') }}"
              class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="grid items-end gap-4 md:grid-cols-[1fr_16rem_13rem_auto_auto]">
                <label class="block text-sm font-medium text-slate-700">
                    Título, identificador, autor, tema o editorial
                    <input
                        type="search"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        maxlength="100"
                        placeholder="Escribí lo que querés buscar"
                        class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Tipo de material
                    <select name="material_type_id"
                            class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Todos los tipos</option>
                        @foreach ($materialTypes as $type)
                            <option value="{{ $type->id }}" @selected(($filters['material_type_id'] ?? '') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Estado del registro
                    <select name="status"
                            class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="active" @selected(($filters['status'] ?? 'active') === 'active')>Activos</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivos</option>
                        <option value="all" @selected(($filters['status'] ?? '') === 'all')>Todos</option>
                    </select>
                </label>

                <button type="submit"
                        class="rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">
                    Buscar
                </button>

                <a href="{{ route('materials.search') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Limpiar
                </a>
            </div>
        </form>

        <div class="mb-3 text-sm text-slate-600">
            {{ $materials->total() }} {{ $materials->total() === 1 ? 'resultado' : 'resultados' }}
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            @if ($materials->isEmpty())
                <p class="p-6 text-slate-600">No se encontraron materiales con esos criterios.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-4 py-3">Título</th>
                                <th class="px-4 py-3">Autores</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Editorial</th>
                                <th class="px-4 py-3">Identificador</th>
                                <th class="px-4 py-3">Temas</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($materials as $material)
                                <tr class="align-top hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $material->title }}</td>
                                    <td class="px-4 py-3">
                                        {{ $material->authors->pluck('name')->join(', ') ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3">{{ $material->materialType?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $material->publisher?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($material->identifier_value)
                                            {{ $material->identifierType?->name }}: {{ $material->identifier_value }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $material->topics->pluck('name')->join(', ') ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($material->is_active)
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Activo</span>
                                        @else
                                            <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-medium text-slate-700">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <a href="{{ route('materials.edit', $material) }}"
                                               class="font-medium text-blue-700 hover:underline">
                                                Editar
                                            </a>

                                            @if ($material->is_active)
                                                <form method="POST"
                                                      action="{{ route('materials.deactivate', $material) }}"
                                                      onsubmit="return confirm('¿Desactivar este material? Sus ejemplares y préstamos no se modificarán.')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="font-medium text-red-700 hover:underline">
                                                        Desactivar
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('materials.activate', $material) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="font-medium text-green-700 hover:underline">
                                                        Activar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $materials->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
