<x-layouts.app title="Ubicaciones">
    <section class="mx-auto max-w-5xl">
        <h1 class="text-2xl font-semibold text-slate-800">Ubicaciones y contenedores</h1>
        <p class="mt-2 text-slate-600">
            Cada ubicación puede tener varios contenedores.
        </p>

        @if (session('success'))
            <p class="mt-4 rounded-lg bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </p>
        @endif

        <form method="POST" action="{{ route('locations.store') }}"
              class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            @csrf

            <h2 class="mb-3 text-lg font-semibold text-slate-800">Registrar ubicación</h2>

            <label for="location-name" class="catalog-label">Nombre *</label>
            <div class="mt-1 flex flex-col gap-3 sm:flex-row">
                <input
                    id="location-name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    class="catalog-input !mt-0 flex-1"
                    placeholder="Ej.: Planta baja"
                >
                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-white">
                    Guardar ubicación
                </button>
            </div>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </form>

        <div class="mt-6 space-y-4">
            @forelse ($locations as $location)
                <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">{{ $location->name }}</h2>

                    <h3 class="mt-4 text-sm font-semibold text-slate-700">Contenedores</h3>

                    @if ($location->containers->isEmpty())
                        <p class="mt-2 text-sm text-slate-500">
                            Esta ubicación todavía no tiene contenedores.
                        </p>
                    @else
                        <ul class="mt-2 list-inside list-disc text-sm text-slate-700">
                            @foreach ($location->containers as $container)
                                <li>{{ $container->name }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <form method="POST"
                          action="{{ route('locations.containers.store', $location) }}"
                          class="mt-4 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row">
                        @csrf

                        <label for="container-{{ $location->id }}" class="sr-only">
                            Nombre del contenedor para {{ $location->name }}
                        </label>
                        <input
                            id="container-{{ $location->id }}"
                            name="container_name"
                            required
                            maxlength="255"
                            class="catalog-input !mt-0 flex-1"
                            placeholder="Nombre del contenedor"
                        >

                        <button type="submit"
                                class="rounded-md border border-blue-700 px-4 py-2 text-blue-700 hover:bg-blue-50">
                            Agregar contenedor
                        </button>

                        @error('container_name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </article>
            @empty
                <p class="rounded-lg bg-white p-5 text-slate-600">
                    Todavía no hay ubicaciones registradas.
                </p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $locations->links() }}
        </div>
    </section>
</x-layouts.app>