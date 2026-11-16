<x-layouts.app title="Registrar ejemplar">
    <section class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <a href="{{ route('items.create') }}" class="text-sm text-blue-700 hover:underline">
            ← Buscar otro material
        </a>

        <h1 class="mt-3 text-2xl font-semibold text-slate-800">Registrar ejemplar</h1>

        <div class="mt-4 rounded-lg bg-slate-50 p-4">
            <p class="font-medium">{{ $material->title }}</p>
            <p class="text-sm text-slate-600">
                {{ $material->authors->pluck('name')->join(', ') ?: 'Sin autor' }}
                · {{ $material->materialType?->name }}
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-5 rounded-lg bg-red-100 p-4 text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('items.store', $material) }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="item_status_id" class="catalog-label">Estado *</label>
                <select id="item_status_id" name="item_status_id" required class="catalog-select">
                    <option value="">Seleccionar estado</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected(old('item_status_id') == $status->id)>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @error('item_status_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="item_condition_id" class="catalog-label">Condición *</label>
                <select id="item_condition_id" name="item_condition_id" required class="catalog-select">
                    <option value="">Seleccionar condición</option>
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition->id }}" @selected(old('item_condition_id') == $condition->id)>
                            {{ $condition->name }}
                        </option>
                    @endforeach
                </select>
                @error('item_condition_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="container_id" class="catalog-label">Ubicación *</label>
                <select id="container_id" name="container_id" required class="catalog-select">
                    <option value="">Seleccionar ubicación</option>
                    @foreach ($containers as $container)
                        <option value="{{ $container->id }}" @selected(old('container_id') == $container->id)>
                            {{ $container->location?->name }} — {{ $container->name }}
                        </option>
                    @endforeach
                </select>
                @error('container_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                @if ($containers->isEmpty())
                    <p class="mt-1 text-sm text-red-600">
                        No hay contenedores configurados. Debe existir una ubicación con un contenedor antes de registrar ejemplares.
                    </p>
                @endif
            </div>

            <div>
                <label for="observation" class="catalog-label">Observación *</label>
                <textarea id="observation" name="observation" rows="4" required class="catalog-input">{{ old('observation') }}</textarea>
                @error('observation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <p class="text-sm text-slate-600">
                El sistema generará un código de barras único al guardar el ejemplar.
            </p>

            <div class="flex justify-end gap-3">
                <a href="{{ route('items.create') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">
                    Cancelar
                </a>
                <button type="submit" class="rounded-md bg-slate-700 px-5 py-2 text-white">
                    Guardar ejemplar
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>