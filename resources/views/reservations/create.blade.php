<x-layouts.opac :title="'Reservar ' . $material->title . ' | Archivium'">
    <section class="mx-auto max-w-2xl">
        <a href="{{ route('opac.materials.show', $material) }}" class="text-sm font-medium text-blue-700 hover:underline">← Volver al material</a>

        <article class="mt-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <h1 class="text-2xl font-semibold text-slate-900">Confirmar reserva</h1>
            <dl class="mt-5 divide-y divide-slate-100 text-sm">
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Título</dt><dd>{{ $material->title }}</dd></div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Tipo</dt><dd>{{ $material->materialType?->name ?? '—' }}</dd></div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Autoría</dt><dd>{{ $material->authors->pluck('name')->join(', ') ?: 'No informada' }}</dd></div>
                @if ($preview['immediate'])
                    <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Estado</dt><dd>Disponible para reservar</dd></div>
                    <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Ubicación</dt><dd>{{ $preview['item']->container?->location?->name ?? 'No informada' }}@if ($preview['item']->container) — {{ $preview['item']->container->name }}@endif</dd></div>
                    <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Retiro hasta</dt><dd>{{ $preview['pickup_deadline']->format('d/m/Y H:i') }}</dd></div>
                @else
                    <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Estado</dt><dd>Sin ejemplares disponibles; ingreso a cola de espera</dd></div>
                    <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Puesto estimado</dt><dd>{{ $preview['queue_position'] }} de {{ \App\Services\ReservationService::QUEUE_LIMIT }}</dd></div>
                    <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Plazo de aviso</dt><dd>Se te enviará un correo cuando haya un ejemplar. Tendrás 6 horas para confirmar.</dd></div>
                @endif
            </dl>

            @if ($errors->has('reservation'))
                <p class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ $errors->first('reservation') }}</p>
            @endif

            <form method="POST" action="{{ route('reservations.store', $material) }}" class="mt-6">
                @csrf
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="confirm" value="1" required class="mt-1 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                    Confirmo que deseo reservar este material y acepto el plazo de retiro indicado.
                </label>
                @error('confirm')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                <button type="submit" class="mt-5 rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">Confirmar reserva</button>
            </form>
        </article>
    </section>
</x-layouts.opac>
