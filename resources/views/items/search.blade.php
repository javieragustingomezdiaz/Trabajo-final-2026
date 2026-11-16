<x-layouts.app title="Buscar ejemplares">
    <section class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Ejemplares</h1>
                <p class="mt-2 text-slate-600">
                    Buscá copias por código de barras, material o ubicación.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('items.create') }}"
                   class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white">
                    Registrar ejemplar
                </a>

                <a href="{{ route('locations.index') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">
                    Ubicaciones y contenedores
                </a>
            </div>
        </div>

        @if (session('success'))
            <p class="mt-4 rounded-lg bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </p>
        @endif

        <form method="GET" action="{{ route('items.search') }}"
              class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <label class="block text-sm font-medium text-slate-700">
                    Texto de búsqueda
                    <input
                        type="search"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="Código, título, autor, ubicación..."
                        class="catalog-input"
                    >
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Estado del registro
                    <select name="status" class="catalog-select">
                        <option value="active" @selected(($filters['status'] ?? 'active') === 'active')>Activos</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivos</option>
                        <option value="all" @selected(($filters['status'] ?? '') === 'all')>Todos</option>
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Estado
                    <select name="item_status_id" class="catalog-select">
                        <option value="">Todos</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}"
                                @selected(($filters['item_status_id'] ?? '') == $status->id)>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Condición
                    <select name="item_condition_id" class="catalog-select">
                        <option value="">Todas</option>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->id }}"
                                @selected(($filters['item_condition_id'] ?? '') == $condition->id)>
                                {{ $condition->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Ubicación
                    <select name="location_id" class="catalog-select">
                        <option value="">Todas</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                @selected(($filters['location_id'] ?? '') == $location->id)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Contenedor
                    <select name="container_id" class="catalog-select">
                        <option value="">Todos</option>
                        @foreach ($containers as $container)
                            <option value="{{ $container->id }}"
                                @selected(($filters['container_id'] ?? '') == $container->id)>
                                {{ $container->location?->name }} — {{ $container->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-4 flex gap-3">
                <button type="submit"
                        class="rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white">
                    Buscar
                </button>

                <a href="{{ route('items.search') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">
                    Limpiar
                </a>
            </div>
        </form>

        <p class="mb-3 mt-5 text-sm text-slate-600">
            {{ $items->total() }} {{ $items->total() === 1 ? 'ejemplar encontrado' : 'ejemplares encontrados' }}
        </p>

        <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            @if ($items->isEmpty())
                <p class="p-5 text-slate-600">No se encontraron ejemplares.</p>
            @else
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Código</th>
                            <th class="px-4 py-3">Material</th>
                            <th class="px-4 py-3">Ubicación</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Condición</th>
                            <th class="px-4 py-3">Registro</th>
                            <th class="px-4 py-3">Observación</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($items as $item)
                            <tr class="align-top {{ $item->is_active ? '' : 'bg-slate-50 text-slate-500' }}">
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $item->barcode }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->material?->title ?? 'Material no disponible' }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $item->material?->authors->pluck('name')->join(', ') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->container?->location?->name ?? '—' }}
                                    @if ($item->container)
                                        — {{ $item->container->name }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $item->status?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $item->condition?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Activo</span>
                                    @else
                                        <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-medium text-slate-700">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $item->observation }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a href="{{ route('items.edit', $item) }}"
                                           class="font-medium text-blue-700 hover:underline">
                                            Editar
                                        </a>

                                        @if ($item->is_active)
                                            <form method="POST"
                                                  action="{{ route('items.deactivate', $item) }}"
                                                  onsubmit="return confirm('¿Desactivar este ejemplar? Se conservarán sus datos e historial.')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="font-medium text-red-700 hover:underline">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('items.activate', $item) }}">
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
            @endif
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </section>
</x-layouts.app>
