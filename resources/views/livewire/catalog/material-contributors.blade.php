<div class="space-y-8 text-sm text-slate-700">
    <section>
        <h2 class="mb-3 catalog-section-title">Autores</h2>

        <div class="flex flex-col gap-2 sm:flex-row">
            <input
                type="search"
                wire:model.live.debounce.300ms="authorSearch"
                placeholder="Buscar autor por nombre"
                class="catalog-input !mt-0 min-w-0 flex-1"
            >
            <button
                type="button"
                wire:click="toggleNewAuthorForm"
                class="catalog-action-secondary"
            >
                {{ $showNewAuthorForm ? 'Cancelar nuevo autor' : '+ Registrar nuevo autor' }}
            </button>
        </div>

        @if ($authors->isNotEmpty())
            <ul class="mt-2 rounded border">
                @foreach ($authors as $author)
                    <li wire:key="author-result-{{ $author->id }}" class="border-b last:border-b-0">
                        <button
                            type="button"
                            wire:click="addAuthor({{ $author->id }})"
                            class="w-full p-2 text-left text-sm hover:bg-gray-50"
                        >
                            {{ $author->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        @elseif (mb_strlen($authorSearch) >= 2)
            <p class="mt-2 text-sm text-slate-500">No se encontraron autores.</p>
        @endif

        @if ($showNewAuthorForm)
            <div class="mt-4 rounded-lg border bg-slate-50 p-4">
                <h3 class="mb-3 font-medium">Registrar un autor</h3>
                <div class="grid gap-3 md:grid-cols-2">
                    <label>
                        <span class="catalog-label mb-1">Nombre *</span>
                        <input wire:model="newAuthorName" class="catalog-input">
                        @error('newAuthorName') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        <span class="catalog-label mb-1">Tipo de autor *</span>
                        <select wire:model="newAuthorTypeId" class="catalog-select">
                            <option value="">Seleccionar</option>
                            @foreach ($authorTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                        @error('newAuthorTypeId') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        <span class="catalog-label mb-1">Fecha de nacimiento</span>
                        <input type="date" wire:model="newAuthorBirthDate" class="catalog-input">
                        @error('newAuthorBirthDate') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        <span class="catalog-label mb-1">Fecha de fallecimiento</span>
                        <input type="date" wire:model="newAuthorDeathDate" class="catalog-input">
                        @error('newAuthorDeathDate') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </label>
                </div>
                <button
                    type="button"
                    wire:click="addNewAuthor"
                    class="catalog-action-primary mt-3"
                >
                    Agregar al material
                </button>
            </div>
        @endif

        @foreach ($selectedAuthors as $id => $name)
            <div wire:key="selected-author-{{ $id }}" class="mt-2 flex items-center justify-between rounded bg-gray-50 p-2">
                <span>{{ $name }}</span>
                <button type="button" wire:click="removeAuthor({{ $id }})" class="text-red-700">Quitar</button>
                <input type="hidden" name="author_ids[]" value="{{ $id }}">
            </div>
        @endforeach

        @foreach ($newAuthors as $index => $author)
            <div wire:key="new-author-{{ $index }}" class="mt-2 flex items-center justify-between rounded bg-blue-50 p-2">
                <span>{{ $author['name'] }} <small>(nuevo)</small></span>
                <button type="button" wire:click="removeNewAuthor({{ $index }})" class="text-red-700">Quitar</button>
                <input type="hidden" name="new_authors[{{ $index }}][name]" value="{{ $author['name'] }}">
                <input type="hidden" name="new_authors[{{ $index }}][author_type_id]" value="{{ $author['author_type_id'] }}">
                <input type="hidden" name="new_authors[{{ $index }}][birth_date]" value="{{ $author['birth_date'] }}">
                <input type="hidden" name="new_authors[{{ $index }}][death_date]" value="{{ $author['death_date'] }}">
            </div>
        @endforeach
    </section>

    <section>
        <h2 class="mb-3 catalog-section-title">Temas</h2>

        <div class="flex flex-col gap-2 sm:flex-row">
            <input
                type="search"
                wire:model.live.debounce.300ms="topicSearch"
                placeholder="Buscar tema"
                class="catalog-input !mt-0 min-w-0 flex-1"
            >
            <button
                type="button"
                wire:click="toggleNewTopicForm"
                class="catalog-action-secondary"
            >
                {{ $showNewTopicForm ? 'Cancelar nuevo tema' : '+ Registrar nuevo tema' }}
            </button>
        </div>

        @if ($topics->isNotEmpty())
            <ul class="mt-2 rounded border">
                @foreach ($topics as $topic)
                    <li wire:key="topic-result-{{ $topic->id }}" class="border-b last:border-b-0">
                        <button
                            type="button"
                            wire:click="addTopic({{ $topic->id }})"
                            class="w-full p-2 text-left text-sm hover:bg-gray-50"
                        >
                            {{ $topic->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        @elseif (mb_strlen($topicSearch) >= 2)
            <p class="mt-2 text-sm text-slate-500">No se encontraron temas.</p>
        @endif

        @if ($showNewTopicForm)
            <div class="mt-4 rounded-lg border bg-slate-50 p-4">
                <h3 class="mb-3 font-medium">Registrar un tema</h3>
                <label>
                    <span class="catalog-label mb-1">Nombre del tema *</span>
                    <input wire:model="newTopicName" class="catalog-input">
                    @error('newTopicName') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </label>
                <button
                    type="button"
                    wire:click="addNewTopic"
                    class="catalog-action-primary mt-3"
                >
                    Agregar al material
                </button>
            </div>
        @endif

        @foreach ($selectedTopics as $id => $name)
            <div wire:key="selected-topic-{{ $id }}" class="mt-2 flex items-center justify-between rounded bg-gray-50 p-2">
                <span>{{ $name }}</span>
                <button type="button" wire:click="removeTopic({{ $id }})" class="text-red-700">Quitar</button>
                <input type="hidden" name="topic_ids[]" value="{{ $id }}">
            </div>
        @endforeach

        @foreach ($newTopics as $index => $topic)
            <div wire:key="new-topic-{{ $index }}" class="mt-2 flex items-center justify-between rounded bg-blue-50 p-2">
                <span>{{ $topic['name'] }} <small>(nuevo)</small></span>
                <button type="button" wire:click="removeNewTopic({{ $index }})" class="text-red-700">Quitar</button>
                <input type="hidden" name="new_topics[{{ $index }}][name]" value="{{ $topic['name'] }}">
            </div>
        @endforeach
    </section>
</div>
