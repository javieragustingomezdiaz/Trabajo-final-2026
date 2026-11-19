<div class="space-y-6">
    <input type="hidden" name="member_id" value="{{ $selectedMember?->id }}">
    <input type="hidden" name="material_id" value="{{ $selectedMaterial?->id }}">

    <section>
        <h2 class="mb-3 text-base font-semibold text-slate-800">1. Seleccionar socio</h2>

        @if ($selectedMember)
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div>
                    <p class="font-medium text-slate-900">
                        {{ $selectedMember->person?->name }} {{ $selectedMember->person?->last_name }}
                    </p>
                    <p class="mt-1 text-sm text-slate-600">
                        DNI: {{ $selectedMember->person?->dni }} · {{ $selectedMember->person?->email }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Socio seleccionado</p>
                </div>
                <button type="button" wire:click="clearMember" class="text-sm font-medium text-blue-800 hover:underline">
                    Cambiar socio
                </button>
            </div>
        @else
            <label for="member-search" class="catalog-label">Buscar por nombre, apellido, email o DNI</label>
            <input
                id="member-search"
                type="search"
                wire:model.live.debounce.300ms="memberSearch"
                autocomplete="off"
                class="catalog-input !mt-1"
                placeholder="Escribí al menos dos caracteres"
            >

            @if (mb_strlen(trim($memberSearch)) >= 2)
                <ul class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm" role="listbox" aria-label="Socios encontrados">
                    @forelse ($members as $member)
                        <li wire:key="staff-reservation-member-{{ $member->id }}">
                            <button
                                type="button"
                                wire:click="selectMember({{ $member->id }})"
                                class="w-full border-b border-slate-100 px-4 py-3 text-left last:border-b-0 hover:bg-slate-50"
                            >
                                <span class="block font-medium text-slate-800">
                                    {{ $member->person?->name }} {{ $member->person?->last_name }}
                                </span>
                                <span class="mt-1 block text-sm text-slate-500">
                                    DNI: {{ $member->person?->dni }} · {{ $member->person?->email }}
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-slate-500">No se encontraron socios activos.</li>
                    @endforelse
                </ul>
            @endif
        @endif
    </section>

    <section @class(['opacity-50' => ! $selectedMember])>
        <h2 class="mb-3 text-base font-semibold text-slate-800">2. Seleccionar material</h2>

        @if ($selectedMaterial)
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div>
                    <p class="font-medium text-slate-900">{{ $selectedMaterial->title }}</p>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $selectedMaterial->materialType?->name ?? 'Tipo no informado' }}
                        @if ($selectedMaterial->identifier_value)
                            · {{ $selectedMaterial->identifier_value }}
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Material seleccionado</p>
                </div>
                <button type="button" wire:click="clearMaterial" class="text-sm font-medium text-blue-800 hover:underline">
                    Cambiar material
                </button>
            </div>
        @elseif ($selectedMember)
            <label for="material-search" class="catalog-label">Buscar por título, autor o identificador</label>
            <input
                id="material-search"
                type="search"
                wire:model.live.debounce.300ms="materialSearch"
                autocomplete="off"
                class="catalog-input !mt-1"
                placeholder="Escribí al menos dos caracteres"
            >

            @if (mb_strlen(trim($materialSearch)) >= 2)
                <ul class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm" role="listbox" aria-label="Materiales encontrados">
                    @forelse ($materials as $material)
                        <li wire:key="staff-reservation-material-{{ $material->id }}">
                            <button
                                type="button"
                                wire:click="selectMaterial({{ $material->id }})"
                                class="w-full border-b border-slate-100 px-4 py-3 text-left last:border-b-0 hover:bg-slate-50"
                            >
                                <span class="block font-medium text-slate-800">{{ $material->title }}</span>
                                <span class="mt-1 block text-sm text-slate-500">
                                    {{ $material->materialType?->name ?? 'Tipo no informado' }}
                                    @if ($material->identifier_value)
                                        · {{ $material->identifier_value }}
                                    @endif
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-slate-500">No se encontraron materiales activos.</li>
                    @endforelse
                </ul>
            @endif
        @else
            <p class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">
                Primero seleccioná un socio para habilitar la búsqueda de materiales.
            </p>
        @endif
    </section>

    @if ($selectedMember && $selectedMaterial)
        <button type="submit" class="rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">
            Registrar reserva
        </button>
    @endif
</div>
