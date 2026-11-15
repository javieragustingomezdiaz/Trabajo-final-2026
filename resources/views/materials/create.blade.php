<x-layouts.app :title="(isset($material) ? 'Editar ' : 'Registrar ').$materialType->name">
    @php
        $material = $material ?? null;
        $inputClass = 'catalog-input';
        $selectClass = 'catalog-select';
        $isEditing = isset($material);
        $specializationRecord = $isEditing
            ? $material->{$specialization['relation']}
            : null;
    @endphp

    <section class="mx-auto max-w-4xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <a href="{{ $isEditing ? route('materials.search') : route('materials.create') }}"
           class="text-sm text-blue-700 hover:underline">
            ← {{ $isEditing ? 'Volver a buscar' : 'Cambiar tipo' }}
        </a>

        <h1 class="mt-3 text-2xl font-semibold text-slate-800">
            {{ $isEditing ? 'Editar material' : 'Registrar material' }}: {{ $materialType->name }}
        </h1>

        <form method="POST"
              action="{{ $isEditing ? route('materials.update', $material) : route('materials.store', $materialType) }}"
              class="mt-6 space-y-8">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            @if ($isEditing)
                <livewire:catalog.material-contributors :material-id="$material->id" />
            @else
                <livewire:catalog.material-contributors />
            @endif

            <section>
                <h2 class="mb-4 catalog-section-title">
                    Datos del material
                </h2>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="title" class="catalog-label">Título *</label>
                        <input id="title" name="title" value="{{ old('title', $material->title ?? '') }}"
                               required maxlength="255" class="{{ $inputClass }}">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="edition" class="catalog-label">Edición</label>
                        <input id="edition" name="edition" value="{{ old('edition', $material->edition ?? '') }}"
                               class="{{ $inputClass }}">
                        @error('edition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="publication_date" class="catalog-label">Fecha de publicación</label>
                        <input id="publication_date" type="date" name="publication_date"
                               value="{{ old('publication_date', $material?->publication_date?->format('Y-m-d')) }}" class="{{ $inputClass }}">
                        @error('publication_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="identifier_value" class="catalog-label">Identificador</label>
                        <input id="identifier_value" name="identifier_value"
                               value="{{ old('identifier_value', $material->identifier_value ?? '') }}" class="{{ $inputClass }}">
                        @error('identifier_value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="identifier_type_id" class="catalog-label">Tipo de identificador *</label>
                        <select id="identifier_type_id" name="identifier_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($identifierTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('identifier_type_id', $material->identifier_type_id ?? '') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('identifier_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="publisher_id" class="catalog-label">Editorial *</label>
                        <select id="publisher_id" name="publisher_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->id }}" @selected(old('publisher_id', $material->publisher_id ?? '') == $publisher->id)>
                                    {{ $publisher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('publisher_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="language_id" class="catalog-label">Idioma *</label>
                        <select id="language_id" name="language_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($languages as $item)
                                <option value="{{ $item->id }}" @selected(old('language_id', $material->language_id ?? '') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('language_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="media_type_id" class="catalog-label">Tipo de medio *</label>
                        <select id="media_type_id" name="media_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($mediaTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('media_type_id', $material->media_type_id ?? '') == $item->id)>
                                    {{ $item->term }}
                                </option>
                            @endforeach
                        </select>
                        @error('media_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="content_type_id" class="catalog-label">Tipo de contenido *</label>
                        <select id="content_type_id" name="content_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($contentTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('content_type_id', $material->content_type_id ?? '') == $item->id)>
                                    {{ $item->term }}
                                </option>
                            @endforeach
                        </select>
                        @error('content_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="carrier_type_id" class="catalog-label">Tipo de soporte *</label>
                        <select id="carrier_type_id" name="carrier_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($carrierTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('carrier_type_id', $material->carrier_type_id ?? '') == $item->id)>
                                    {{ $item->term }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="dimension" class="catalog-label">Dimensión</label>
                        <input id="dimension" name="dimension" value="{{ old('dimension', $material->dimension ?? '') }}"
                               class="{{ $inputClass }}">
                        @error('dimension') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="extent" class="catalog-label">Extensión</label>
                        <input id="extent" name="extent" value="{{ old('extent', $material->extent ?? '') }}"
                               class="{{ $inputClass }}">
                        @error('extent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="cover" class="catalog-label">Ruta de portada</label>
                        <input id="cover" name="cover" value="{{ old('cover', $material->cover ?? '') }}"
                               class="{{ $inputClass }}">
                        @error('cover') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="catalog-label">Descripción</label>
                        <textarea id="description" name="description" rows="3"
                                  class="{{ $inputClass }}">{{ old('description', $material->description ?? '') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section>
                <h2 class="mb-4 catalog-section-title">
                    Datos específicos: {{ $materialType->name }}
                </h2>

                <div class="grid gap-4 md:grid-cols-2">
                    @switch($specialization['relation'])
                        @case('bibliographic')
                            <label class="flex items-center gap-2 md:col-span-2">
                                <input type="checkbox" name="is_illustrated" value="1"
                                       @checked(old('is_illustrated', $specializationRecord?->is_illustrated))
                                       class="rounded border-slate-300">
                                <span>Incluye ilustraciones</span>
                            </label>
                            <div>
                                <label for="binding_type" class="catalog-label">Tipo de encuadernación</label>
                                <input id="binding_type" name="binding_type" value="{{ old('binding_type', $specializationRecord?->binding_type ?? '') }}"
                                       class="{{ $inputClass }}">
                            </div>
                            @break

                        @case('serialpublication')
                            <div>
                                <label for="frequency" class="catalog-label">Frecuencia</label>
                                <input id="frequency" name="frequency" value="{{ old('frequency', $specializationRecord?->frequency ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="start_year" class="catalog-label">Año de inicio</label>
                                <input id="start_year" type="number" name="start_year" value="{{ old('start_year', $specializationRecord?->start_year ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="end_year" class="catalog-label">Año de finalización</label>
                                <input id="end_year" type="number" name="end_year" value="{{ old('end_year', $specializationRecord?->end_year ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="initial_numeric_designation" class="catalog-label">Designación inicial</label>
                                <input id="initial_numeric_designation" name="initial_numeric_designation" value="{{ old('initial_numeric_designation', $specializationRecord?->initial_numeric_designation ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            @break

                        @case('cartographic')
                            @foreach (['scale' => 'Escala', 'projection' => 'Proyección', 'coordinate' => 'Coordenadas', 'orientation' => 'Orientación'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="catalog-label">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $specializationRecord?->{$field} ?? '') }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            @break

                        @case('graph')
                            @foreach (['technical' => 'Técnica', 'color' => 'Color'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="catalog-label">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $specializationRecord?->{$field} ?? '') }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            @break

                        @case('musical')
                            <div>
                                <label for="musical_notation" class="catalog-label">Notación musical</label>
                                <input id="musical_notation" name="musical_notation" value="{{ old('musical_notation', $specializationRecord?->musical_notation ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="performance_medium" class="catalog-label">Medio de interpretación</label>
                                <textarea id="performance_medium" name="performance_medium" rows="2" class="{{ $inputClass }}">{{ old('performance_medium', $specializationRecord?->performance_medium ?? '') }}</textarea>
                            </div>
                            @break

                        @case('sound')
                            <div>
                                <label for="duration_seconds" class="catalog-label">Duración en segundos</label>
                                <input id="duration_seconds" type="number" min="0" name="duration_seconds" value="{{ old('duration_seconds', $specializationRecord?->duration_seconds ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            @foreach (['recording_type' => 'Tipo de grabación', 'audio_channel' => 'Canal de audio', 'playback_speed' => 'Velocidad de reproducción'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="catalog-label">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $specializationRecord?->{$field} ?? '') }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            <div>
                                <label for="number_tracks" class="catalog-label">Cantidad de pistas</label>
                                <input id="number_tracks" type="number" min="0" name="number_tracks" value="{{ old('number_tracks', $specializationRecord?->number_tracks ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            @break

                        @case('video')
                            <div>
                                <label for="duration_seconds" class="catalog-label">Duración en segundos</label>
                                <input id="duration_seconds" type="number" min="0" name="duration_seconds" value="{{ old('duration_seconds', $specializationRecord?->duration_seconds ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            @foreach (['video_format' => 'Formato de video', 'aspect_ratio' => 'Relación de aspecto'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="catalog-label">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $specializationRecord?->{$field} ?? '') }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            @break

                        @case('object')
                            <div>
                                <label for="number_pieces" class="catalog-label">Cantidad de piezas</label>
                                <input id="number_pieces" type="number" min="1" name="number_pieces" value="{{ old('number_pieces', $specializationRecord?->number_pieces ?? 1) }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="manufacter" class="catalog-label">Fabricante</label>
                                <input id="manufacter" name="manufacter" value="{{ old('manufacter', $specializationRecord?->manufacter ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="serial_number" class="catalog-label">Número de serie</label>
                                <input id="serial_number" name="serial_number" value="{{ old('serial_number', $specializationRecord?->serial_number ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="requires_assembly" value="1"
                                       @checked(old('requires_assembly', $specializationRecord?->requires_assembly))
                                       class="rounded border-slate-300">
                                <span>Requiere ensamblaje</span>
                            </label>
                            @break
                    @endswitch
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ $isEditing ? route('materials.search') : route('materials.create') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-md bg-slate-700 px-5 py-2 font-medium text-white hover:bg-slate-800">
                    {{ $isEditing ? 'Guardar cambios' : 'Guardar material' }}
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>
