<div class="space-y-6">
    <input type="hidden" name="member_id" value="{{ $selectedMember?->id }}">
    <input type="hidden" name="item_id" value="{{ $selectedItem?->id }}">

    <section>
        <h2 class="mb-3 text-base font-semibold text-slate-800">1. Seleccionar socio</h2>

        @if ($selectedMember)
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div>
                    <p class="font-medium text-slate-900">{{ $selectedMember->person?->name }} {{ $selectedMember->person?->last_name }}</p>
                    <p class="mt-1 text-sm text-slate-600">DNI: {{ $selectedMember->person?->dni }} · {{ $selectedMember->person?->email }}</p>
                    <p class="mt-1 text-xs text-slate-500">Socio seleccionado</p>
                </div>
                <button type="button" wire:click="clearMember" class="text-sm font-medium text-blue-800 hover:underline">Cambiar socio</button>
            </div>
        @else
            <label for="circulation-member-search" class="catalog-label">Buscar por nombre, apellido, email o DNI</label>
            <input id="circulation-member-search" type="search" wire:model.live.debounce.300ms="memberSearch" autocomplete="off" class="catalog-input !mt-1" placeholder="Escribí al menos dos caracteres">

            @if (mb_strlen(trim($memberSearch)) >= 2)
                <ul class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm" role="listbox" aria-label="Socios encontrados">
                    @forelse ($members as $member)
                        <li wire:key="circulation-member-{{ $member->id }}">
                            <button type="button" wire:click="selectMember({{ $member->id }})" class="w-full border-b border-slate-100 px-4 py-3 text-left last:border-b-0 hover:bg-slate-50">
                                <span class="block font-medium text-slate-800">{{ $member->person?->name }} {{ $member->person?->last_name }}</span>
                                <span class="mt-1 block text-sm text-slate-500">DNI: {{ $member->person?->dni }} · {{ $member->person?->email }}</span>
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
        <h2 class="mb-3 text-base font-semibold text-slate-800">2. Seleccionar ejemplar</h2>

        @if ($selectedItem)
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div>
                    <p class="font-medium text-slate-900">{{ $selectedItem->material?->title }}</p>
                    <p class="mt-1 text-sm text-slate-600">Código: {{ $selectedItem->barcode }} · {{ $selectedItem->material?->materialType?->name }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $selectedItem->status?->name }} · {{ $selectedItem->container?->location?->name ?? 'Ubicación no informada' }}@if ($selectedItem->container) — {{ $selectedItem->container->name }}@endif
                    </p>
                </div>
                <button type="button" wire:click="clearItem" class="text-sm font-medium text-blue-800 hover:underline">Cambiar ejemplar</button>
            </div>
        @elseif ($selectedMember)
            <label for="circulation-item-search" class="catalog-label">Buscar por código de barras, título, autor o identificador</label>
            <input id="circulation-item-search" type="search" wire:model.live.debounce.300ms="itemSearch" autocomplete="off" class="catalog-input !mt-1" placeholder="Escribí al menos dos caracteres">

            @if (mb_strlen(trim($itemSearch)) >= 2)
                <ul class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm" role="listbox" aria-label="Ejemplares disponibles">
                    @forelse ($items as $item)
                        <li wire:key="circulation-item-{{ $item->id }}">
                            <button type="button" wire:click="selectItem({{ $item->id }})" class="w-full border-b border-slate-100 px-4 py-3 text-left last:border-b-0 hover:bg-slate-50">
                                <span class="block font-medium text-slate-800">{{ $item->material?->title }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $item->barcode }} · {{ $item->material?->materialType?->name }} · {{ $item->status?->name }}</span>
                            </button>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-slate-500">No se encontraron ejemplares disponibles para este socio.</li>
                    @endforelse
                </ul>
            @endif
        @else
            <p class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">Primero seleccioná un socio para buscar ejemplares.</p>
        @endif
    </section>

    @if ($selectedItem)
        <section>
            <h2 class="mb-3 text-base font-semibold text-slate-800">3. Tipo de préstamo</h2>
            @if (count($loanTypes))
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($loanTypes as $type)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 hover:bg-slate-50">
                            <input type="radio" name="loan_type" value="{{ $type['value'] }}" wire:model="loanType" class="mt-1 border-slate-300 text-blue-700 focus:ring-blue-600">
                            <span>
                                <span class="block text-sm font-medium text-slate-800">{{ $type['label'] }}</span>
                                <span class="mt-1 block text-xs text-slate-500">Duración según regla del material: {{ $type['duration'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('loan_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="mt-5 rounded-md bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800">Registrar préstamo</button>
            @else
                <p class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">El tipo de material no tiene modalidades de préstamo habilitadas en su regla.</p>
            @endif
        </section>
    @endif
</div>
