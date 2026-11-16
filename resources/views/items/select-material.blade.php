<x-layouts.app title="Registrar ejemplar">
    <section class="mx-auto max-w-6xl">
        <h1 class="text-2xl font-semibold text-slate-800">Registrar ejemplar</h1>
        <p class="mt-2 text-slate-600">Buscar el material al que pertenece la copia.</p>

        <a href="{{ route('locations.index') }}"
            class="mt-4 inline-block rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Administrar ubicaciones y contenedores
        </a>

        @if (session('success'))
            <p class="mt-4 rounded-lg bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </p>
        @endif

        <form method="GET" action="{{ route('items.create') }}"
              class="mt-6 flex flex-col gap-3 rounded-xl bg-white p-5 shadow-sm sm:flex-row">
            <input
                type="search"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Título, autor o identificador"
                class="min-w-0 flex-1 rounded-md border border-slate-300 px-3 py-2"
            >
            <button class="rounded-md bg-blue-700 px-4 py-2 text-white">
                Buscar material
            </button>
        </form>

        <div class="mt-5 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            @forelse ($materials as $material)
                <div class="flex flex-wrap items-center justify-between gap-4 border-b p-4 last:border-b-0">
                    <div>
                        <h2 class="font-semibold text-slate-800">{{ $material->title }}</h2>
                        <p class="text-sm text-slate-600">
                            {{ $material->authors->pluck('name')->join(', ') ?: 'Sin autor' }}
                            · {{ $material->materialType?->name }}
                            @if ($material->identifier_value)
                                · {{ $material->identifier_value }}
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('items.form', $material) }}"
                       class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white">
                        Seleccionar
                    </a>
                </div>
            @empty
                <p class="p-5 text-slate-600">No hay materiales que coincidan con la búsqueda.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $materials->links() }}
        </div>
    </section>
</x-layouts.app>