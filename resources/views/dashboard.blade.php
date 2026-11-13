<x-layouts.app title="Panel">
    <div class="-m-4 min-h-screen bg-stone-100 p-4 md:-m-8 md:p-8">
        <h1 class="text-2xl font-semibold text-slate-800">
            Bienvenido, {{ auth()->user()->person?->name ?? 'usuario' }}
        </h1>

    </div>
</x-layouts.app>