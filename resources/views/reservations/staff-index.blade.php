<x-layouts.app title="Buscar reservas | Archivium">
    <section class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Buscar reservas</h1>
            <p class="mt-2 text-slate-600">Buscá reservas activas o consultá su historial, incluidas las canceladas y vencidas.</p>
        </div>

        @if (session('status'))
            <p class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status">
                {{ session('status') }}
            </p>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-medium">No se pudo completar la operación. Revisá los datos indicados.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" action="{{ route('staff-reservations.index') }}"
              class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <label class="block text-sm font-medium text-slate-700">
                    Fecha desde
                    <input
                        type="date"
                        name="fecha_desde"
                        value="{{ $filters['fecha_desde'] ?? '' }}"
                        class="catalog-input"
                    >
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Fecha hasta
                    <input
                        type="date"
                        name="fecha_hasta"
                        value="{{ $filters['fecha_hasta'] ?? '' }}"
                        class="catalog-input"
                    >
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Nombre o apellido del socio
                    <input
                        type="search"
                        name="socio"
                        value="{{ $filters['socio'] ?? '' }}"
                        maxlength="100"
                        class="catalog-input"
                        placeholder="Nombre o apellido"
                    >
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Código de barras del ejemplar
                    <input
                        type="search"
                        name="codigo_barras"
                        value="{{ $filters['codigo_barras'] ?? '' }}"
                        maxlength="100"
                        class="catalog-input"
                        placeholder="Ej.: ARC-000123"
                    >
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Tipo de reserva
                    <select name="tipo" class="catalog-select">
                        <option value="">Todos los tipos</option>
                        <option value="inmediata" @selected(($filters['tipo'] ?? '') === 'inmediata')>Inmediata</option>
                        <option value="cola" @selected(($filters['tipo'] ?? '') === 'cola')>Cola de espera</option>
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Alcance
                    <select name="alcance" class="catalog-select">
                        <option value="">Activas (predeterminado)</option>
                        <option value="activas" @selected(($filters['alcance'] ?? '') === 'activas')>Solo activas</option>
                        <option value="historial" @selected(($filters['alcance'] ?? '') === 'historial')>Historial</option>
                        <option value="todas" @selected(($filters['alcance'] ?? '') === 'todas')>Todas las reservas</option>
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Estado de reserva
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
                <a href="{{ route('staff-reservations.index') }}"
                   class="rounded-md border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Limpiar
                </a>
                <button type="submit" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">
                    Buscar reservas
                </button>
            </div>
        </form>

        @if ($searchAttempted && ! $hasCriteria)
            <p class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900" role="alert">
                Debe ingresar al menos un criterio de búsqueda.
            </p>
        @elseif (! $searchAttempted)
            <p class="mt-5 rounded-lg bg-white p-4 text-slate-600 shadow-sm ring-1 ring-slate-200">
                Ingresá al menos un criterio. Si no elegís otro alcance, se mostrarán las reservas activas.
            </p>
        @elseif ($reservations->isEmpty())
            <p class="mt-5 rounded-lg bg-white p-4 text-slate-600 shadow-sm ring-1 ring-slate-200" role="status">
                No se encontraron reservas que coincidan con los criterios ingresados.
            </p>
        @else
            <div class="mt-6 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Fecha de registro</th>
                            <th class="px-4 py-3">Fecha de finalización</th>
                            <th class="px-4 py-3">Socio</th>
                            <th class="px-4 py-3">Material / ejemplar</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $reservation->registration_date?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-4 py-4">
                                    @if ($reservation->withdrawal_date)
                                        {{ $reservation->withdrawal_date->format('d/m/Y H:i') }}
                                    @elseif ($reservation->cancelled_at)
                                        {{ $reservation->cancelled_at->format('d/m/Y H:i') }}
                                    @elseif ($reservation->status?->name === \App\Models\Reservation::STATUS_EXPIRED)
                                        {{ $reservation->updated_at?->format('d/m/Y H:i') ?? 'Vencida' }}
                                    @elseif ($reservation->withdrawal_deadline)
                                        {{ $reservation->withdrawal_deadline->format('d/m/Y H:i') }}
                                    @elseif ($reservation->confirmation_expires_at)
                                        Confirmar antes de {{ $reservation->confirmation_expires_at->format('d/m/Y H:i') }}
                                    @else
                                        Pendiente
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    {{ $reservation->user?->person?->name ?? '' }}
                                    {{ $reservation->user?->person?->last_name ?? 'Socio no disponible' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-medium text-slate-800">
                                        {{ $reservation->material?->title ?? 'Material no disponible' }}
                                    </span>
                                    <span class="mt-1 block text-slate-500">
                                        Código de barras: {{ $reservation->item?->barcode ?? 'Sin ejemplar asignado' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $reservation->notified_at || ! $reservation->item_id ? 'Cola de espera' : 'Inmediata' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $reservation->status?->name ?? 'Sin estado' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($reservation->isCancellable())
                                        <a href="{{ route('staff-reservations.cancel-form', $reservation) }}"
                                           class="font-medium text-red-700 hover:underline">
                                            Cancelar
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $reservations->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
