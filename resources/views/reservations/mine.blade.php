<x-layouts.app title="Mis reservas | Archivium">
    <section class="mx-auto max-w-5xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Mis reservas</h1>
            <p class="mt-2 text-slate-600">Consultá tus reservas activas y el historial.</p>
        </div>

        @if (session('status'))
            <p class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->has('reservation'))
            <p class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{{ $errors->first('reservation') }}</p>
        @endif

        <nav class="mb-5 flex gap-2 border-b border-slate-200" aria-label="Vistas de reservas">
            <a
                href="{{ route('reservations.mine', ['vista' => 'activas']) }}"
                @class([
                    'border-b-2 px-4 py-3 text-sm font-medium',
                    'border-blue-700 text-blue-700' => $vista === 'activas',
                    'border-transparent text-slate-600 hover:text-slate-900' => $vista !== 'activas',
                ])
            >
                Reservas activas
            </a>

            <a
                href="{{ route('reservations.mine', ['vista' => 'historial']) }}"
                @class([
                    'border-b-2 px-4 py-3 text-sm font-medium',
                    'border-blue-700 text-blue-700' => $vista === 'historial',
                    'border-transparent text-slate-600 hover:text-slate-900' => $vista !== 'historial',
                ])
            >
                Historial
            </a>
        </nav>

        @if ($reservations->isEmpty())
            <div class="rounded-lg border border-slate-200 bg-white p-6 text-slate-600 shadow-sm">
                @if (! auth()->user()->reservations()->exists())
                    Aún no tiene reservas registradas.
                @elseif ($vista === 'activas')
                    No tenés reservas activas.
                @else
                    Todavía no tenés reservas en el historial.
                @endif
            </div>
        @else
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Registro</th>
                            <th class="px-4 py-3">Finalización</th>
                            <th class="px-4 py-3">Material</th>
                            <th class="px-4 py-3">Tipo de reserva</th>
                            <th class="px-4 py-3">Estado</th>
                            @if ($vista === 'activas')
                                <th class="px-4 py-3">Acción</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $reservation->registration_date?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($reservation->withdrawal_deadline)
                                        {{ $reservation->withdrawal_deadline->format('d/m/Y H:i') }}
                                    @elseif ($reservation->confirmation_expires_at)
                                        Confirmar antes de {{ $reservation->confirmation_expires_at->format('d/m/Y H:i') }}
                                    @else
                                        Pendiente
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <a
                                        href="{{ route('opac.materials.show', $reservation->material) }}"
                                        class="font-medium text-blue-700 hover:underline"
                                    >
                                        {{ $reservation->material?->title ?? 'Material no disponible' }}
                                    </a>
                                    <div class="mt-1 text-slate-500">
                                        {{ $reservation->material?->materialType?->name ?? '—' }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $reservation->notified_at || ! $reservation->item_id ? 'Cola de espera' : 'Directa' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $reservation->status?->name ?? 'Sin estado' }}
                                </td>
                                @if ($vista === 'activas')
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <form method="POST" action="{{ route('reservations.cancel-own', $reservation) }}"
                                              onsubmit="return confirm('¿Querés cancelar esta reserva?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="font-medium text-red-700 hover:underline">
                                                Cancelar
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $reservations->links() }}
            </div>
        @endif

        <a href="{{ route('opac.index') }}" class="mt-6 inline-block text-sm font-medium text-blue-700 hover:underline">
            Volver al catálogo
        </a>
    </section>
</x-layouts.app>
