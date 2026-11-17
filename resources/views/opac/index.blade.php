<x-layouts.opac title="Consultar catálogo | Archivium">
    <section>
        <div class="mb-6">
            <h1 class="text-3xl font-semibold text-slate-900">Consultar catálogo</h1>
            <p class="mt-2 text-slate-600">Buscá materiales activos de la biblioteca por uno o varios criterios.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-medium">Revisá los criterios ingresados.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" action="{{ route('opac.index') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 sm:flex-row">
                <label for="q" class="sr-only">Buscar en el catálogo</label>
                <input
                    id="q"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    maxlength="100"
                    class="catalog-input mt-0 min-h-12 flex-1 text-base"
                    placeholder="Título, autor, tema, ISBN o editorial..."
                >
                <button type="submit" class="rounded-md bg-blue-700 px-6 py-3 text-sm font-medium text-white hover:bg-blue-800">
                    Buscar
                </button>
            </div>

            <details class="mt-4 border-t border-slate-200 pt-3" @if ($advancedFiltersActive) open @endif>
                <summary class="cursor-pointer select-none text-sm font-medium text-blue-700 hover:text-blue-800">
                    Filtros avanzados
                </summary>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <label class="block text-sm font-medium text-slate-700">
                    Título
                    <input name="title" value="{{ $filters['title'] ?? '' }}" maxlength="100" class="catalog-input" placeholder="Título o parte del título">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Autor / creador
                    <input name="author" value="{{ $filters['author'] ?? '' }}" maxlength="100" class="catalog-input" placeholder="Nombre del autor o creador">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Tema
                    <input name="topic" value="{{ $filters['topic'] ?? '' }}" maxlength="100" class="catalog-input" placeholder="Tema o materia">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Código identificador
                    <input name="identifier" value="{{ $filters['identifier'] ?? '' }}" maxlength="100" class="catalog-input" placeholder="ISBN u otro identificador">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Tipo de material
                    <select name="material_type_id" class="catalog-select">
                        <option value="">Todos los tipos</option>
                        @foreach ($materialTypes as $type)
                            <option value="{{ $type->id }}" @selected(($filters['material_type_id'] ?? '') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Editorial
                    <input name="publisher" value="{{ $filters['publisher'] ?? '' }}" maxlength="100" class="catalog-input" placeholder="Nombre de la editorial">
                </label>

                <label class="block text-sm font-medium text-slate-700">
                    Año de publicación
                    <input type="number" name="publication_year" value="{{ $filters['publication_year'] ?? '' }}" min="1000" max="9999" class="catalog-input" placeholder="Ej.: 2024">
                </label>
            </div>
            </details>

            <div class="mt-4 flex justify-end">
                <a href="{{ route('opac.index') }}" class="rounded-md border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Limpiar
                </a>
            </div>
        </form>

        @if ($searchAttempted && ! $hasCriteria)
            <p class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900" role="alert">
                Se debe ingresar al menos un criterio de búsqueda.
            </p>
        @elseif (! $searchAttempted)
            <p class="mt-5 rounded-lg bg-white p-4 text-slate-600 shadow-sm ring-1 ring-slate-200">
                Ingresá al menos un criterio para consultar el catálogo.
            </p>
        @elseif ($materials->isEmpty())
            <p class="mt-5 rounded-lg bg-white p-4 text-slate-600 shadow-sm ring-1 ring-slate-200">
                No se encontraron resultados según los criterios de búsqueda.
            </p>
        @else
          <div class="mx-auto mt-6 max-w-3xl">
            <div class="mb-3 text-sm text-slate-600">
                {{ $materials->total() }} {{ $materials->total() === 1 ? 'material encontrado' : 'materiales encontrados' }}
            </div>

            <div class="space-y-5">
                @foreach ($materials as $material)
                    <article class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <div class="flex items-start gap-4">
                            @if ($material->cover_url)
                                <img src="{{ $material->cover_url }}" alt="Portada de {{ $material->title }}"
                                     class="h-45 w-35 shrink-0 rounded border border-slate-200 bg-slate-50 object-contain">
                            @else
                                <div class="flex h-36 w-28 shrink-0 items-center justify-center rounded border border-slate-200 bg-slate-50 text-center text-xs text-slate-500">
                                    Sin portada
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h2 class="text-xl font-semibold leading-6 text-slate-900">
                                    <a href="{{ route('opac.materials.show', $material) }}" class="hover:text-blue-700 hover:underline">
                                        {{ $material->title }}
                                    </a>
                                </h2>
                                <p class="mt-1 text-base text-slate-600">
                                    {{ $material->authors->pluck('name')->join(', ') ?: 'Autoría no informada' }}
                                </p>
                                <div class="mt-2 space-y-1 text-base text-slate-700">
                                    <p><span class="text-slate-500">Tipo:</span> {{ $material->materialType?->name ?? '—' }}</p>
                                    <p><span class="text-slate-500">Año:</span> {{ $material->publication_date?->format('Y') ?? '—' }}</p>
                                    <p><span class="text-slate-500">Ejemplares activos:</span> {{ $material->active_copies_count }}</p>
                                    <p><span class="text-slate-500">Disponibles:</span> {{ $material->available_copies_count }}</p>
                                </div>
                                <a href="{{ route('opac.materials.show', $material) }}" class="mt-2 inline-block text-base font-medium text-blue-700 hover:underline">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">{{ $materials->links() }}</div>
          </div>
        @endif
    </section>
</x-layouts.opac>
