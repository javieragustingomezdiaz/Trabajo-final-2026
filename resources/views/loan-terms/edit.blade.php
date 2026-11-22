<x-layouts.app :title="'Regla de préstamo | ' . $materialType->name">
    <div class="mb-6">
        <a href="{{ route('loan-terms.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Reglas de préstamo</a>
        <h1 class="mt-3 text-2xl font-semibold text-slate-900">{{ $loanTerm ? 'Modificar regla' : 'Configurar regla' }}</h1>
        <p class="mt-1 text-sm text-slate-600">Tipo de material: <span class="font-medium text-slate-800">{{ $materialType->name }}</span></p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-md bg-red-50 p-4 text-sm text-red-800">
            <p class="font-medium">Revisá los datos ingresados.</p>
            <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $numberFields = [
            'hours_on_standby' => ['Tiempo para retirar una reserva', 'Horas'],
            'home_loan_days' => ['Duración del préstamo', 'Días'],
            'in_library_loan_hours' => ['Duración de la consulta', 'Horas'],
            'max_renewal' => ['Cantidad máxima de renovaciones', 'Veces'],
            'renewal_days' => ['Duración de cada renovación', 'Días'],
            'max_simultaneous_reservations' => ['Reservas simultáneas por socio', 'Reservas', 'Dejalo vacío si no querés establecer un límite.'],
            'max_simultaneous_loans' => ['Préstamos simultáneos por socio', 'Préstamos', 'Dejalo vacío si no querés establecer un límite.'],
            'sanctions_days_factor' => ['Factor de días de sanción', 'Días', 'Días de suspensión por cada día de demora.'],
            'damage_suspension_days' => ['Suspensión por daño', 'Días'],
        ];
    @endphp

    <form method="POST" action="{{ route('loan-terms.update', $materialType) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Reservas</h2>
                <p class="mt-1 text-sm text-slate-500">Indicá si el material admite reservas y cuánto tiempo se mantiene una oferta.</p>
                <label class="mt-4 flex items-center gap-3 rounded-lg bg-slate-50 p-3 text-sm font-medium text-slate-800">
                    <input type="hidden" name="bookable" value="0">
                    <input type="checkbox" name="bookable" value="1" @checked((bool) old('bookable', $loanTerm?->bookable ?? false)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                    <span>Permite reservar</span>
                </label>
                <div class="mt-4">
                    @include('loan-terms.partials.number-field', ['name' => 'hours_on_standby', 'field' => $numberFields['hours_on_standby']])
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Préstamo a domicilio</h2>
                <p class="mt-1 text-sm text-slate-500">Configurá si el socio puede llevarse el ejemplar y por cuánto tiempo.</p>
                <label class="mt-4 flex items-center gap-3 rounded-lg bg-slate-50 p-3 text-sm font-medium text-slate-800">
                    <input type="hidden" name="is_home_loanable" value="0">
                    <input type="checkbox" name="is_home_loanable" value="1" @checked((bool) old('is_home_loanable', $loanTerm?->is_home_loanable ?? false)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                    <span>Permite préstamo a domicilio</span>
                </label>
                <div class="mt-4">
                    @include('loan-terms.partials.number-field', ['name' => 'home_loan_days', 'field' => $numberFields['home_loan_days']])
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Consulta en sala</h2>
                <p class="mt-1 text-sm text-slate-500">Configurá el uso dentro de la biblioteca y su duración máxima.</p>
                <label class="mt-4 flex items-center gap-3 rounded-lg bg-slate-50 p-3 text-sm font-medium text-slate-800">
                    <input type="hidden" name="allow_in_library_use" value="0">
                    <input type="checkbox" name="allow_in_library_use" value="1" @checked((bool) old('allow_in_library_use', $loanTerm?->allow_in_library_use ?? false)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                    <span>Permite consulta en sala</span>
                </label>
                <div class="mt-4">
                    @include('loan-terms.partials.number-field', ['name' => 'in_library_loan_hours', 'field' => $numberFields['in_library_loan_hours']])
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Renovaciones</h2>
                <p class="mt-1 text-sm text-slate-500">Definí si un préstamo puede renovarse y los límites para hacerlo.</p>
                <label class="mt-4 flex items-center gap-3 rounded-lg bg-slate-50 p-3 text-sm font-medium text-slate-800">
                    <input type="hidden" name="is_renewable" value="0">
                    <input type="checkbox" name="is_renewable" value="1" @checked((bool) old('is_renewable', $loanTerm?->is_renewable ?? false)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                    <span>Permite renovar préstamos</span>
                </label>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @include('loan-terms.partials.number-field', ['name' => 'max_renewal', 'field' => $numberFields['max_renewal']])
                    @include('loan-terms.partials.number-field', ['name' => 'renewal_days', 'field' => $numberFields['renewal_days']])
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Límites por socio</h2>
                <p class="mt-1 text-sm text-slate-500">Estos topes son independientes: uno limita reservas y el otro préstamos activos.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @include('loan-terms.partials.number-field', ['name' => 'max_simultaneous_reservations', 'field' => $numberFields['max_simultaneous_reservations']])
                    @include('loan-terms.partials.number-field', ['name' => 'max_simultaneous_loans', 'field' => $numberFields['max_simultaneous_loans']])
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Sanciones</h2>
                <p class="mt-1 text-sm text-slate-500">Parámetros para calcular suspensiones por demora o daño.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @include('loan-terms.partials.number-field', ['name' => 'sanctions_days_factor', 'field' => $numberFields['sanctions_days_factor']])
                    @include('loan-terms.partials.number-field', ['name' => 'damage_suspension_days', 'field' => $numberFields['damage_suspension_days']])
                </div>
            </section>
        </div>

        <div class="flex flex-wrap gap-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <button type="submit" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">Guardar regla</button>
            <a href="{{ route('loan-terms.index') }}" class="rounded-md border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</x-layouts.app>
