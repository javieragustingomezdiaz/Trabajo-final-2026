<x-layouts.app :title="'Parámetros globales'">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Parámetros globales</h1>
        <p class="mt-1 text-sm text-slate-600">Configurá los límites generales que se aplican a la circulación de la biblioteca.</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-md bg-green-100 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-md bg-red-50 p-4 text-sm text-red-800">
            <p class="font-medium">Revisá los datos ingresados.</p>
            <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $fields = [
            'max_simultaneous_reservations' => ['Reservas simultáneas por socio', 'Reservas'],
            'reservation_offer_hours' => ['Plazo para responder una oferta de reserva', 'Horas', 'Al vencer, la oferta pasa al siguiente socio de la cola.'],
            'max_simultaneous_loans' => ['Préstamos simultáneos por socio', 'Préstamos'],
            'max_loan_days' => ['Duración máxima de préstamos a domicilio', 'Días'],
            'max_consultation_hours' => ['Duración máxima de consulta en sala', 'Horas'],
            'max_renewals' => ['Cantidad máxima de renovaciones', 'Veces'],
            'grace_hours' => ['Horas de gracia por demora', 'Horas'],
            'max_sanction_days' => ['Duración máxima de una sanción', 'Días'],
        ];
    @endphp

    <form method="POST" action="{{ route('global-rules.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Límites por socio</h2>
            <p class="mt-1 text-sm text-slate-500">Estos máximos se aplican al total de materiales, sin importar su tipo.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach (['max_simultaneous_reservations', 'max_simultaneous_loans'] as $name)
                    @include('global-rules.partials.number-field', ['name' => $name, 'field' => $fields[$name]])
                @endforeach
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Plazos de circulación</h2>
            <p class="mt-1 text-sm text-slate-500">Establecé los topes generales para préstamos, consulta y ofertas de reserva.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach (['reservation_offer_hours', 'max_loan_days', 'max_consultation_hours'] as $name)
                    @include('global-rules.partials.number-field', ['name' => $name, 'field' => $fields[$name]])
                @endforeach
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Renovaciones y sanciones</h2>
            <p class="mt-1 text-sm text-slate-500">Definí el límite global de renovaciones y los parámetros de gracia y suspensión.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach (['max_renewals', 'grace_hours', 'max_sanction_days'] as $name)
                    @include('global-rules.partials.number-field', ['name' => $name, 'field' => $fields[$name]])
                @endforeach
            </div>
        </section>

        <div class="flex flex-wrap gap-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <button type="submit" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">Guardar parámetros</button>
        </div>
    </form>
</x-layouts.app>
