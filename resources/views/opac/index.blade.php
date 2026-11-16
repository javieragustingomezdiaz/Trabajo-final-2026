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
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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

            <div class="mt-5 flex flex-wrap gap-3">
                <button type="submit" class="rounded-md bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800">
                    Buscar
                </button>
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
            <div class="mb-3 mt-6 text-sm text-slate-600">
                {{ $materials->total() }} {{ $materials->total() === 1 ? 'material encontrado' : 'materiales encontrados' }}
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($materials as $material)
                    <article class="flex flex-col rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                <a href="{{ route('opac.materials.show', $material) }}" class="hover:text-blue-700 hover:underline">
                                    {{ $material->title }}
                                </a>
                            </h2>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $material->authors->pluck('name')->join(', ') ?: 'Autoría no informada' }}
                            </p>
                            <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                                <dt class="text-slate-500">Tipo</dt>
                                <dd>{{ $material->materialType?->name ?? '—' }}</dd>
                                <dt class="text-slate-500">Soporte</dt>
                                <dd>{{ $material->carrierType?->name ?? '—' }}</dd>
                                <dt class="text-slate-500">Año</dt>
                                <dd>{{ $material->publication_date?->format('Y') ?? '—' }}</dd>
                                <dt class="text-slate-500">Ejemplares activos</dt>
                                <dd>{{ $material->active_copies_count }}</dd>
                                <dt class="text-slate-500">Disponibles</dt>
                                <dd>{{ $material->available_copies_count }}</dd>
                            </dl>
                        </div>
                        <a href="{{ route('opac.materials.show', $material) }}" class="mt-5 font-medium text-blue-700 hover:underline">
                            Ver detalles
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">{{ $materials->links() }}</div>
        @endif
    </section>
</x-layouts.opac>
