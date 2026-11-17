<x-layouts.opac :title="$material->title . ' | Catálogo Archivium'">
    <section class="mx-auto max-w-3xl">
        <a href="{{ route('opac.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Volver al catálogo</a>

        <article class="mt-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <section class="border-b border-slate-200 pb-6">
                <div class="flex flex-col gap-10 sm:flex-row">
                    <div class="w-48 shrink-0">
                        @if ($material->cover_url)
                            <img src="{{ $material->cover_url }}" alt="Portada de {{ $material->title }}"
                                 class="max-h-80 w-full rounded-lg border border-slate-200 object-contain object-top">
                        @else
                            <div class="flex h-64 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-500">
                                Sin portada
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg font-semibold text-slate-900">Datos bibliográficos</h2>
                        @php
                            $specializationLabels = [
                                'is_illustrated' => 'Incluye ilustraciones',
                                'binding_type' => 'Tipo de encuadernación',
                                'frequency' => 'Frecuencia',
                                'start_year' => 'Año de inicio',
                                'end_year' => 'Año de finalización',
                                'initial_numeric_designation' => 'Designación inicial',
                                'scale' => 'Escala',
                                'projection' => 'Proyección',
                                'coordinate' => 'Coordenadas',
                                'orientation' => 'Orientación',
                                'technical' => 'Técnica',
                                'color' => 'Color',
                                'musical_notation' => 'Notación musical',
                                'performance_medium' => 'Medio de interpretación',
                                'duration_seconds' => 'Duración en segundos',
                                'recording_type' => 'Tipo de grabación',
                                'audio_channel' => 'Canal de audio',
                                'playback_speed' => 'Velocidad de reproducción',
                                'number_tracks' => 'Cantidad de pistas',
                                'video_format' => 'Formato de video',
                                'aspect_ratio' => 'Relación de aspecto',
                                'number_pieces' => 'Cantidad de piezas',
                                'requires_assembly' => 'Requiere ensamblaje',
                                'manufacter' => 'Fabricante',
                                'serial_number' => 'Número de serie',
                            ];
                        @endphp
                        <dl class="mt-2 divide-y divide-slate-100 text-sm">
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Título</dt>
                                <dd><h1 class=" font-semibold text-slate-900">{{ $material->title }}</h1></dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Autoría</dt>
                                <dd class="text-slate-800">{{ $material->authors->pluck('name')->join(', ') ?: 'Autoría no informada' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Tipo de material</dt>
                                <dd class="text-slate-800">{{ $material->materialType?->name ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Temas</dt>
                                <dd class="text-slate-800">{{ $material->topics->pluck('name')->join(', ') ?: 'Sin temas registrados' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Edición</dt>
                                <dd class="text-slate-800">{{ $material->edition ?: '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Editorial</dt>
                                <dd class="text-slate-800">{{ $material->publisher?->name ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Año de publicación</dt>
                                <dd class="text-slate-800">{{ $material->publication_date?->format('Y') ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Identificador</dt>
                                <dd class="text-slate-800">{{ $material->identifier_value ? (($material->identifierType?->name ? $material->identifierType->name . ': ' : '') . $material->identifier_value) : '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Idioma</dt>
                                <dd class="text-slate-800">{{ $material->language?->name ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Extensión</dt>
                                <dd class="text-slate-800">{{ $material->extent ?: '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Dimensiones</dt>
                                <dd class="text-slate-800">{{ $material->dimension ?: '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Tipo de contenido</dt>
                                <dd class="text-slate-800">{{ $material->contentType?->term ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Tipo de medio</dt>
                                <dd class="text-slate-800">{{ $material->mediaType?->term ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Tipo de soporte</dt>
                                <dd class="text-slate-800">{{ $material->carrierType?->term ?? '—' }}</dd>
                            </div>
                            @foreach ($specializationValues as $field => $value)
                                <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                    <dt class="font-medium text-slate-500">
                                        {{ $specializationLabels[$field] ?? $field }}
                                    </dt>
                                    <dd class="text-slate-800">
                                        @if (in_array($field, $specialization['booleans'], true))
                                            {{ $value ? 'Sí' : 'No' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                            <div class="grid grid-cols-[7rem_minmax(0,1fr)] gap-3 py-3 sm:grid-cols-[10rem_minmax(0,1fr)]">
                                <dt class="font-medium text-slate-500">Resumen</dt>
                                <dd class="whitespace-pre-line leading-6 text-slate-800">{{ $material->description ?: 'No hay resumen disponible.' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            <section class="pt-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Ejemplares</h2>
                    </div>
                </div>

                <div>
                    <p class="flex flex-wrap items-end justify-between gap-3 my-2 text-slate-800">
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
