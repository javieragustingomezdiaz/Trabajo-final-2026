<x-layouts.app title="Buscar préstamos | Archivium">
    <section class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Buscar préstamos</h1>
            <p class="mt-2 text-slate-600">Buscá préstamos registrados por fecha, socio, título o estado.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-medium">Los criterios de búsqueda no son válidos. Revisá los datos ingresados.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" action="{{ route('circulations.index') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <label class="block text-sm font-medium text-slate-700">
                    Fecha desde
                    <input type="date" name="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}" class="catalog-input">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Fecha hasta
                    <input type="date" name="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}" class="catalog-input">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Nombre o apellido del socio
                    <input type="search" name="socio" value="{{ $filters['socio'] ?? '' }}" maxlength="100" class="catalog-input" placeholder="Nombre o apellido">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Título del material
                    <input type="search" name="titulo" value="{{ $filters['titulo'] ?? '' }}" maxlength="150" class="catalog-input" placeholder="Título">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Estado del préstamo
                    <select name="estado_id" class="catalog-select">
                        <option value="">Todos los estados</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" @selected(($filters['estado_id'] ?? '') == $status->id)>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-5 flex flex-wrap justify-end gap-3">
                <a href="{{ route('circulations.index') }}" class="rounded-md border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Limpiar
                </a>
                <button type="submit" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">
                    Buscar préstamos
                </button>
            </div>
        </form>

        @if ($searchAttempted && ! $hasCriteria)
            <p class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900" role="alert">
                Ingresá al menos un criterio de búsqueda.
            </p>
        @elseif (! $searchAttempted)
            <p class="mt-5 rounded-lg bg-white p-4 text-slate-600 shadow-sm ring-1 ring-slate-200">
                Ingresá al menos un criterio para buscar préstamos.
            </p>
        @elseif ($circulations->isEmpty())
            <p class="mt-5 rounded-lg bg-white p-4 text-slate-600 shadow-sm ring-1 ring-slate-200" role="status">
                No se encontraron préstamos según los criterios ingresados.
            </p>
        @else
            <p class="mb-3 mt-6 text-sm text-slate-600">
                {{ $circulations->total() }} {{ $circulations->total() === 1 ? 'préstamo encontrado' : 'préstamos encontrados' }}
            </p>

            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Fecha del préstamo</th>
                            <th class="px-4 py-3">Devolución estimada</th>
                            <th class="px-4 py-3">Socio</th>
                            <th class="px-4 py-3">Material / ejemplar</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($circulations as $circulation)
                            <tr class="align-top">
                                <td class="whitespace-nowrap px-4 py-4">{{ $circulation->loan_date?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $circulation->estimated_return_date?->format('d/m/Y H:i') ?? '—' }}
                                    @if ($circulation->actual_return_date)
                                        <span class="mt-1 block text-xs text-slate-500">Devuelto: {{ $circulation->actual_return_date->format('d/m/Y H:i') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    {{ $circulation->user?->person?->name ?? '' }}
                                    {{ $circulation->user?->person?->last_name ?? 'Socio no disponible' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-medium text-slate-800">{{ $circulation->item?->material?->title ?? 'Material no disponible' }}</span>
                                    <span class="mt-1 block text-slate-500">Código de barras: {{ $circulation->item?->barcode ?? 'Ejemplar no disponible' }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $circulation->loan_type }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $circulation->status?->name ?? 'Sin estado' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $circulations->links() }}</div>
        @endif
    </section>
</x-layouts.app>
