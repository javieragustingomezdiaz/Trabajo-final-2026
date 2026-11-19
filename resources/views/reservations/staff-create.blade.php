<x-layouts.app title="Registrar reserva para un socio | Archivium">
    <section class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Registrar reserva para un socio</h1>
            <p class="mt-2 text-slate-600">Primero seleccioná al socio. Después vas a poder buscar y seleccionar el material.</p>
        </div>

        @if (session('status'))
            <p class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status">
                {{ session('status') }}
            </p>
        @endif

        @if ($errors->has('reservation'))
            <p class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                {{ $errors->first('reservation') }}
            </p>
        @endif

        <form method="POST" action="{{ route('staff-reservations.store') }}"
              class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
            @csrf
            <livewire:circulation.staff-reservation-form />

            @error('member_id')
                <p class="mt-3 text-sm text-red-700">{{ $message }}</p>
            @enderror
            @error('material_id')
                <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </form>
    </section>
</x-layouts.app>
