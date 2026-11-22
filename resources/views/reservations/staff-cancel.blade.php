<x-layouts.app title="Cancelar reserva | Archivium">
    <section class="mx-auto max-w-3xl">
        <a href="{{ route('staff-reservations.index') }}" class="text-sm font-medium text-blue-700 hover:underline">
            ← Volver a la búsqueda de reservas
        </a>

        <article class="mt-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <h1 class="text-2xl font-semibold text-slate-800">Cancelar reserva</h1>
            <p class="mt-2 text-slate-600">Confirmá los datos e ingresá la justificación para cancelar la reserva.</p>

            @if ($errors->has('reservation'))
                <p class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    {{ $errors->first('reservation') }}
                </p>
            @endif

            <dl class="mt-5 divide-y divide-slate-100 text-sm">
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Registro</dt>
                    <dd>{{ $reservation->registration_date?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Finalización</dt>
                    <dd>
                        @if ($reservation->withdrawal_deadline)
                            {{ $reservation->withdrawal_deadline->format('d/m/Y H:i') }}
                        @elseif ($reservation->confirmation_expires_at)
                            Confirmación hasta {{ $reservation->confirmation_expires_at->format('d/m/Y H:i') }}
                        @else
                            Pendiente
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Socio</dt>
                    <dd>{{ $reservation->user?->person?->name }} {{ $reservation->user?->person?->last_name }}</dd>
                </div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Material</dt>
                    <dd>{{ $reservation->material?->title ?? 'Material no disponible' }}</dd>
                </div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Tipo</dt>
                    <dd>{{ $reservation->notified_at || ! $reservation->item_id ? 'Cola de espera' : 'Inmediata' }}</dd>
                </div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Estado</dt>
                    <dd>{{ $reservation->status?->name ?? 'Sin estado' }}</dd>
                </div>
                <div class="grid grid-cols-[9rem_minmax(0,1fr)] gap-3 py-3">
                    <dt class="font-medium text-slate-500">Código de barras</dt>
                    <dd>{{ $reservation->item?->barcode ?? 'Sin ejemplar asignado' }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('staff-reservations.cancel', $reservation) }}" class="mt-6">
                @csrf
                @method('PATCH')

                <label for="cancellation_reason" class="block text-sm font-medium text-slate-700">
                    Justificación de la cancelación
                    <textarea
                        id="cancellation_reason"
                        name="cancellation_reason"
                        rows="4"
                        maxlength="1000"
                        required
                        class="catalog-input"
                        placeholder="Explicá brevemente por qué se cancela esta reserva."
                    >{{ old('cancellation_reason') }}</textarea>
                </label>
                @error('cancellation_reason')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror

                <div class="mt-5 flex flex-wrap gap-3">
                    <button type="submit" class="rounded-md bg-red-700 px-5 py-3 text-sm font-medium text-white hover:bg-red-800">
                        Confirmar cancelación
                    </button>
                    <a href="{{ route('staff-reservations.index') }}" class="rounded-md border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Volver
                    </a>
                </div>
            </form>
        </article>
    </section>
</x-layouts.app>
