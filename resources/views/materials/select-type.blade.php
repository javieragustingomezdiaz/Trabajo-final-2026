<x-layouts.app title="Registrar material">
    <section class="mx-auto max-w-4xl">
        <h1 class="text-2xl font-semibold text-slate-800">
            Registrar material
        </h1>

        <p class="mt-2 text-slate-600">
            Primero seleccioná el tipo de material que querés catalogar.
        </p>

        @if (session('success'))
            <p class="mt-4 rounded-lg bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </p>
        @endif

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($materialTypes as $materialType)
                <a href="{{ route('materials.form', $materialType) }}"
                   class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:ring-blue-400">
                    <h2 class="font-semibold text-slate-800">
                        {{ $materialType->name }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $materialType->group }}
                    </p>
                </a>
            @empty
                <p class="text-slate-600">
                    No hay tipos de material registrados.
                </p>
            @endforelse
        </div>
    </section>
</x-layouts.app>