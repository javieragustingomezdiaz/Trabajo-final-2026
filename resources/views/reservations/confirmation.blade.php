<x-layouts.opac :title="'Confirmar disponibilidad | Archivium'">
    <section class="mx-auto max-w-2xl">
        <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <h1 class="text-2xl font-semibold text-slate-900">Hay un ejemplar disponible</h1>
            <p class="mt-2 text-slate-600">Confirmá dentro del plazo indicado para reservarlo a tu nombre.</p>

            <dl class="mt-5 divide-y divide-slate-100 text-sm">
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Título</dt><dd>{{ $reservation->material->title }}</dd></div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Tipo</dt><dd>{{ $reservation->material->materialType?->name ?? '—' }}</dd></div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Autoría</dt><dd>{{ $reservation->material->authors->pluck('name')->join(', ') ?: 'No informada' }}</dd></div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Ubicación</dt><dd>{{ $reservation->item?->container?->location?->name ?? 'No informada' }}@if ($reservation->item?->container) — {{ $reservation->item->container->name }}@endif</dd></div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3"><dt class="font-medium text-slate-500">Confirmar antes de</dt><dd>{{ $reservation->confirmation_expires_at->format('d/m/Y H:i') }}</dd></div>
            </dl>

            <form method="POST" action="{{ URL::temporarySignedRoute('reservations.confirm', $reservation->confirmation_expires_at, ['reservation' => $reservation->id, 'token' => $token]) }}" class="mt-6">
                @csrf
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">Confirmar reserva</button>
            </form>
        </article>
    </section>
</x-layouts.opac>
