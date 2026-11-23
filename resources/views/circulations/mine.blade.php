<x-layouts.app title="Mis préstamos | Archivium">
    <section class="mx-auto max-w-5xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Mis préstamos</h1>
            <p class="mt-2 text-slate-600">Consultá tus préstamos activos y el historial.</p>
        </div>

        <nav class="mb-5 flex gap-2 border-b border-slate-200" aria-label="Vistas de préstamos">
            <a
                href="{{ route('circulations.mine', ['vista' => 'activas']) }}"
                @class([
                    'border-b-2 px-4 py-3 text-sm font-medium',
                    'border-blue-700 text-blue-700' => $vista === 'activas',
                    'border-transparent text-slate-600 hover:text-slate-900' => $vista !== 'activas',
                ])
            >
                Préstamos activos
            </a>

            <a
                href="{{ route('circulations.mine', ['vista' => 'historial']) }}"
                @class([
                    'border-b-2 px-4 py-3 text-sm font-medium',
                    'border-blue-700 text-blue-700' => $vista === 'historial',
                    'border-transparent text-slate-600 hover:text-slate-900' => $vista !== 'historial',
                ])
            >
                Historial
            </a>
        </nav>

        @if ($circulations->isEmpty())
            <div class="rounded-lg border border-slate-200 bg-white p-6 text-slate-600 shadow-sm">
                @if (! auth()->user()->circulations()->exists())
                    Aún no tiene préstamos registrados.
                @elseif ($vista === 'activas')
                    No tenés préstamos activos.
                @else
                    Todavía no tenés préstamos en el historial.
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

                            @if ($vista === 'historial')
                                <th class="px-4 py-3">Estado</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($circulations as $circulation)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $circulation->loan_date?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($vista === 'activas')
                                        {{ $circulation->estimated_return_date?->format('d/m/Y H:i') ?? '—' }}
                                    @else
                                        {{ $circulation->actual_return_date?->format('d/m/Y H:i') ?? '—' }}
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if ($circulation->item?->material)
                                        <a
                                            href="{{ route('opac.materials.show', $circulation->item->material) }}"
                                            class="font-medium text-blue-700 hover:underline"
                                        >
                                            {{ $circulation->item->material->title }}
                                        </a>

                                        <div class="mt-1 text-slate-500">
                                            {{ $circulation->item->material->materialType?->name ?? '—' }}
                                        </div>
                                    @else
                                        Material no disponible
                                    @endif
                                </td>

                                @if ($vista === 'historial')
                                    <td class="whitespace-nowrap px-4 py-4">
                                        {{ $circulation->status?->name ?? 'Sin estado' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $circulations->links() }}
            </div>
        @endif

        <a href="{{ route('opac.index') }}" class="mt-6 inline-block text-sm font-medium text-blue-700 hover:underline">
            Volver al catálogo
        </a>
    </section>
</x-layouts.app>
