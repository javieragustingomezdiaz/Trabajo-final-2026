<x-layouts.opac :title="$material->title . ' | Catálogo Archivium'">
    <section class="mx-auto max-w-5xl">
        <a href="{{ route('opac.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Volver al catálogo</a>

        <article class="mt-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="border-b border-slate-200 pb-5">
                <p class="text-sm font-medium text-blue-700">{{ $material->materialType?->name ?? 'Material' }}</p>
                <h1 class="mt-1 text-3xl font-semibold text-slate-900">{{ $material->title }}</h1>
                <p class="mt-2 text-slate-600">{{ $material->authors->pluck('name')->join(', ') ?: 'Autoría no informada' }}</p>
            </div>

            <div class="grid gap-8 py-6 md:grid-cols-2">
                <section>
                    <h2 class="text-lg font-semibold text-slate-900">Datos bibliográficos</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="font-medium text-slate-500">Editorial</dt><dd>{{ $material->publisher?->name ?? '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Año de publicación</dt><dd>{{ $material->publication_date?->format('Y') ?? '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Edición</dt><dd>{{ $material->edition ?: '—' }}</dd></div>
                        <div>
                            <dt class="font-medium text-slate-500">Identificador</dt>
                            <dd>{{ $material->identifier_value ? (($material->identifierType?->name ? $material->identifierType->name . ': ' : '') . $material->identifier_value) : '—' }}</dd>
                        </div>
                        <div><dt class="font-medium text-slate-500">Extensión</dt><dd>{{ $material->extent ?: '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Dimensiones</dt><dd>{{ $material->dimension ?: '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Idioma</dt><dd>{{ $material->language?->name ?? '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Tipo de soporte</dt><dd>{{ $material->carrierType?->name ?? '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Tipo de medio</dt><dd>{{ $material->mediaType?->name ?? '—' }}</dd></div>
                        <div><dt class="font-medium text-slate-500">Tipo de contenido</dt><dd>{{ $material->contentType?->name ?? '—' }}</dd></div>
                    </dl>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">Temas</h2>
                    <p class="mt-3 text-sm text-slate-700">{{ $material->topics->pluck('name')->join(', ') ?: 'Sin temas registrados' }}</p>

                    <h2 class="mt-7 text-lg font-semibold text-slate-900">Resumen</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $material->description ?: 'No hay resumen disponible.' }}</p>
                </section>
            </div>

            <section class="border-t border-slate-200 pt-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Ejemplares</h2>
                        <p class="mt-1 text-sm text-slate-600">Se muestran disponibilidad y ubicación; el código de barras queda reservado para la gestión interna.</p>
                    </div>
                    <p class="text-sm font-medium text-slate-700">
                        {{ $material->items->where('status.name', 'Disponible')->count() }} disponibles de {{ $material->items->count() }} activos
                    </p>
                </div>

                @if ($material->items->isEmpty())
                    <p class="mt-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">No hay ejemplares activos registrados.</p>
                @else
                    <div class="mt-4 overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                                <tr>
                                    <th class="px-4 py-3">Ubicación</th>
                                    <th class="px-4 py-3">Disponibilidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($material->items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            {{ $item->container?->location?->name ?? 'Ubicación no informada' }}
                                            @if ($item->container)
                                                — {{ $item->container->name }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $item->status?->name ?? 'Estado no informado' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </article>
    </section>
</x-layouts.opac>
