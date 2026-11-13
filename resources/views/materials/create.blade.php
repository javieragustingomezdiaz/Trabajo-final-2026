<x-layouts.app :title="'Registrar '.$materialType->name">
    @php
        $inputClass = 'mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
        $selectClass = 'mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
    @endphp

    <section class="mx-auto max-w-4xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <a href="{{ route('materials.create') }}"
           class="text-sm text-blue-700 hover:underline">
            ← Cambiar tipo
        </a>

        <h1 class="mt-3 text-2xl font-semibold text-slate-800">
            Registrar: {{ $materialType->name }}
        </h1>

        <form method="POST"
              action="{{ route('materials.store', $materialType) }}"
              class="mt-6 space-y-8">
            @csrf

            <section>
                <h2 class="mb-4 text-lg font-semibold text-slate-800">
                    Datos del material
                </h2>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium">Título *</label>
                        <input id="title" name="title" value="{{ old('title') }}"
                               required maxlength="255" class="{{ $inputClass }}">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="edition" class="block text-sm font-medium">Edición</label>
                        <input id="edition" name="edition" value="{{ old('edition') }}"
                               class="{{ $inputClass }}">
                        @error('edition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="publication_date" class="block text-sm font-medium">Fecha de publicación</label>
                        <input id="publication_date" type="date" name="publication_date"
                               value="{{ old('publication_date') }}" class="{{ $inputClass }}">
                        @error('publication_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="identifier_value" class="block text-sm font-medium">Identificador</label>
                        <input id="identifier_value" name="identifier_value"
                               value="{{ old('identifier_value') }}" class="{{ $inputClass }}">
                        @error('identifier_value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="identifier_type_id" class="block text-sm font-medium">Tipo de identificador *</label>
                        <select id="identifier_type_id" name="identifier_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($identifierTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('identifier_type_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('identifier_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="publisher_id" class="block text-sm font-medium">Editorial *</label>
                        <select id="publisher_id" name="publisher_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->id }}" @selected(old('publisher_id') == $publisher->id)>
                                    {{ $publisher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('publisher_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="language_id" class="block text-sm font-medium">Idioma *</label>
                        <select id="language_id" name="language_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($languages as $item)
                                <option value="{{ $item->id }}" @selected(old('language_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('language_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="media_type_id" class="block text-sm font-medium">Tipo de medio *</label>
                        <select id="media_type_id" name="media_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($mediaTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('media_type_id') == $item->id)>
                                    {{ $item->term }}
                                </option>
                            @endforeach
                        </select>
                        @error('media_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="content_type_id" class="block text-sm font-medium">Tipo de contenido *</label>
                        <select id="content_type_id" name="content_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($contentTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('content_type_id') == $item->id)>
                                    {{ $item->term }}
                                </option>
                            @endforeach
                        </select>
                        @error('content_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="carrier_type_id" class="block text-sm font-medium">Tipo de soporte *</label>
                        <select id="carrier_type_id" name="carrier_type_id" required class="{{ $selectClass }}">
                            <option value="">Seleccionar</option>
                            @foreach ($carrierTypes as $item)
                                <option value="{{ $item->id }}" @selected(old('carrier_type_id') == $item->id)>
                                    {{ $item->term }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="dimension" class="block text-sm font-medium">Dimensión</label>
                        <input id="dimension" name="dimension" value="{{ old('dimension') }}"
                               class="{{ $inputClass }}">
                        @error('dimension') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="extent" class="block text-sm font-medium">Extensión</label>
                        <input id="extent" name="extent" value="{{ old('extent') }}"
                               class="{{ $inputClass }}">
                        @error('extent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="cover" class="block text-sm font-medium">Ruta de portada</label>
                        <input id="cover" name="cover" value="{{ old('cover') }}"
                               class="{{ $inputClass }}">
                        @error('cover') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium">Descripción</label>
                        <textarea id="description" name="description" rows="3"
                                  class="{{ $inputClass }}">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section>
                <h2 class="mb-4 text-lg font-semibold text-slate-800">
                    Datos específicos: {{ $materialType->name }}
                </h2>

                <div class="grid gap-4 md:grid-cols-2">
                    @switch($specialization['relation'])
                        @case('bibliographic')
                            <label class="flex items-center gap-2 md:col-span-2">
                                <input type="checkbox" name="is_illustrated" value="1"
                                       @checked(old('is_illustrated'))
                                       class="rounded border-slate-300">
                                <span>Incluye ilustraciones</span>
                            </label>
                            <div>
                                <label for="binding_type" class="block text-sm font-medium">Tipo de encuadernación</label>
                                <input id="binding_type" name="binding_type" value="{{ old('binding_type') }}"
                                       class="{{ $inputClass }}">
                            </div>
                            @break

                        @case('serialpublication')
                            <div>
                                <label for="frequency" class="block text-sm font-medium">Frecuencia</label>
                                <input id="frequency" name="frequency" value="{{ old('frequency') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="start_year" class="block text-sm font-medium">Año de inicio</label>
                                <input id="start_year" type="number" name="start_year" value="{{ old('start_year') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="end_year" class="block text-sm font-medium">Año de finalización</label>
                                <input id="end_year" type="number" name="end_year" value="{{ old('end_year') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="initial_numeric_designation" class="block text-sm font-medium">Designación inicial</label>
                                <input id="initial_numeric_designation" name="initial_numeric_designation" value="{{ old('initial_numeric_designation') }}" class="{{ $inputClass }}">
                            </div>
                            @break

                        @case('cartographic')
                            @foreach (['scale' => 'Escala', 'projection' => 'Proyección', 'coordinate' => 'Coordenadas', 'orientation' => 'Orientación'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            @break

                        @case('graph')
                            @foreach (['technical' => 'Técnica', 'color' => 'Color'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            @break

                        @case('musical')
                            <div>
                                <label for="musical_notation" class="block text-sm font-medium">Notación musical</label>
                                <input id="musical_notation" name="musical_notation" value="{{ old('musical_notation') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="performance_medium" class="block text-sm font-medium">Medio de interpretación</label>
                                <textarea id="performance_medium" name="performance_medium" rows="2" class="{{ $inputClass }}">{{ old('performance_medium') }}</textarea>
                            </div>
                            @break

                        @case('sound')
                            <div>
                                <label for="duration_seconds" class="block text-sm font-medium">Duración en segundos</label>
                                <input id="duration_seconds" type="number" min="0" name="duration_seconds" value="{{ old('duration_seconds') }}" class="{{ $inputClass }}">
                            </div>
                            @foreach (['recording_type' => 'Tipo de grabación', 'audio_channel' => 'Canal de audio', 'playback_speed' => 'Velocidad de reproducción'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            <div>
                                <label for="number_tracks" class="block text-sm font-medium">Cantidad de pistas</label>
                                <input id="number_tracks" type="number" min="0" name="number_tracks" value="{{ old('number_tracks') }}" class="{{ $inputClass }}">
                            </div>
                            @break

                        @case('video')
                            <div>
                                <label for="duration_seconds" class="block text-sm font-medium">Duración en segundos</label>
                                <input id="duration_seconds" type="number" min="0" name="duration_seconds" value="{{ old('duration_seconds') }}" class="{{ $inputClass }}">
                            </div>
                            @foreach (['video_format' => 'Formato de video', 'aspect_ratio' => 'Relación de aspecto'] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
                                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" class="{{ $inputClass }}">
                                </div>
                            @endforeach
                            @break

                        @case('object')
                            <div>
                                <label for="number_pieces" class="block text-sm font-medium">Cantidad de piezas</label>
                                <input id="number_pieces" type="number" min="1" name="number_pieces" value="{{ old('number_pieces', 1) }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="manufacter" class="block text-sm font-medium">Fabricante</label>
                                <input id="manufacter" name="manufacter" value="{{ old('manufacter') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="serial_number" class="block text-sm font-medium">Número de serie</label>
                                <input id="serial_number" name="serial_number" value="{{ old('serial_number') }}" class="{{ $inputClass }}">
                            </div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="requires_assembly" value="1"
                                       @checked(old('requires_assembly'))
                                       class="rounded border-slate-300">
                                <span>Requiere ensamblaje</span>
                            </label>
                            @break
                    @endswitch
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('materials.create') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-md bg-slate-700 px-5 py-2 font-medium text-white hover:bg-slate-800">
                    Guardar material
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>