<x-layouts.app :title="'Reglas de préstamo'">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Reglas de préstamo</h1>
        <p class="mt-1 text-sm text-slate-600">Configurá las opciones de circulación para cada tipo de material.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-100 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
        <table class="w-full text-left text-sm text-slate-700">
            <thead class="bg-slate-100 text-xs uppercase text-slate-600">
                <tr>
                    <th scope="col" class="px-5 py-3">Tipo de material</th>
                    <th scope="col" class="px-5 py-3">Estado de regla</th>
                    <th scope="col" class="px-5 py-3 text-right">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materialTypes as $materialType)
                    <tr class="border-t border-slate-200">
                        <td class="px-5 py-4 font-medium text-slate-900">{{ $materialType->name }}</td>
                        <td class="px-5 py-4">
                            @if ($materialType->loanTerm)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Configurada</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('loan-terms.edit', $materialType) }}" class="font-medium text-blue-700 hover:underline">
                                {{ $materialType->loanTerm ? 'Modificar regla' : 'Configurar regla' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">No hay tipos de material registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $materialTypes->links() }}</div>
</x-layouts.app>
