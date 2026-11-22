<x-layouts.app :title="'Registrar préstamo'">
    <section class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900">Registrar préstamo</h1>
            <p class="mt-1 text-sm text-slate-600">Seleccioná un socio y un ejemplar; el sistema calculará la devolución según las reglas vigentes.</p>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-md bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-md bg-red-50 p-4 text-sm text-red-800">
                <p class="font-medium">No se pudo registrar el préstamo.</p>
                <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('circulations.store') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <livewire:circulation.staff-circulation-form />
        </form>
    </section>
</x-layouts.app>
