<?php

namespace App\Livewire\Catalog;

use App\Models\Author;
use App\Models\AuthorType;
use App\Models\Material;
use App\Models\Topic;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MaterialContributors extends Component
{
    #[Locked]
    public ?int $editingMaterialId = null;

    public string $authorSearch = '';
    public string $topicSearch = '';
    public bool $showNewAuthorForm = false;
    public bool $showNewTopicForm = false;

    // IDs de registros existentes, con el nombre para mostrarlos.
    public array $selectedAuthors = [];
    public array $selectedTopics = [];

    // Autores nuevos, todavía sin guardar en la base de datos.
    public array $newAuthors = [];
    public array $newTopics = [];

    public string $newAuthorName = '';
    public string $newAuthorTypeId = '';
    public string $newAuthorBirthDate = '';
    public string $newAuthorDeathDate = '';
    public string $newTopicName = '';

    /** Authorize the initial render and every subsequent Livewire request. */
    public function boot(): void
    {
        if ($this->editingMaterialId !== null) {
            Gate::authorize('update', Material::findOrFail($this->editingMaterialId));
            return;
        }

        Gate::authorize('create', Material::class);
    }

    public function mount(?int $materialId = null): void
    {
        if ($materialId === null) {
            return;
        }

        $material = Material::query()
            ->with(['authors', 'topics'])
            ->findOrFail($materialId);

        Gate::authorize('update', $material);

        $this->editingMaterialId = $material->id;
        $this->selectedAuthors = $material->authors
            ->mapWithKeys(fn (Author $author) => [$author->id => $author->name])
            ->all();
        $this->selectedTopics = $material->topics
            ->mapWithKeys(fn (Topic $topic) => [$topic->id => $topic->name])
            ->all();
    }

    public function addAuthor(int $id): void
    {
        $author = Author::query()->findOrFail($id);

        $this->selectedAuthors[$author->id] = $author->name;
        $this->authorSearch = '';
    }

    public function removeAuthor(int $id): void
    {
        unset($this->selectedAuthors[$id]);
    }

    public function toggleNewAuthorForm(): void
    {
        $this->showNewAuthorForm = ! $this->showNewAuthorForm;
        $this->resetValidation();
    }

    public function addTopic(int $id): void
    {
        $topic = Topic::query()->findOrFail($id);

        $this->selectedTopics[$topic->id] = $topic->name;
        $this->topicSearch = '';
    }

    public function removeTopic(int $id): void
    {
        unset($this->selectedTopics[$id]);
    }

    public function toggleNewTopicForm(): void
    {
        $this->showNewTopicForm = ! $this->showNewTopicForm;
        $this->resetValidation();
    }

    public function addNewAuthor(): void
    {
        $data = $this->validate([
            'newAuthorName' => ['required', 'string', 'max:255'],
            'newAuthorTypeId' => ['required', 'integer', 'exists:author_types,id'],
            'newAuthorBirthDate' => ['nullable', 'date'],
            'newAuthorDeathDate' => ['nullable', 'date'],
        ]);

        $this->newAuthors[] = [
            'name' => $data['newAuthorName'],
            'author_type_id' => $data['newAuthorTypeId'],
            'birth_date' => $data['newAuthorBirthDate'] ?: null,
            'death_date' => $data['newAuthorDeathDate'] ?: null,
        ];

        $this->reset([
            'newAuthorName',
            'newAuthorTypeId',
            'newAuthorBirthDate',
            'newAuthorDeathDate',
        ]);
        $this->showNewAuthorForm = false;
    }

    public function removeNewAuthor(int $index): void
    {
        unset($this->newAuthors[$index]);
        $this->newAuthors = array_values($this->newAuthors);
    }

    public function addNewTopic(): void
    {
        $data = $this->validate([
            'newTopicName' => ['required', 'string', 'max:255'],
        ]);

        $this->newTopics[] = ['name' => trim($data['newTopicName'])];
        $this->reset('newTopicName');
        $this->showNewTopicForm = false;
    }

    public function removeNewTopic(int $index): void
    {
        unset($this->newTopics[$index]);
        $this->newTopics = array_values($this->newTopics);
    }

    public function render()
    {
        $authors = mb_strlen($this->authorSearch) >= 2
            ? Author::query()
                ->where('name', 'like', '%' . $this->authorSearch . '%')
                ->orderBy('name')
                ->limit(20)
                ->get()
            : collect();

        $topics = mb_strlen($this->topicSearch) >= 2
            ? Topic::query()
                ->where('name', 'like', '%' . $this->topicSearch . '%')
                ->orderBy('name')
                ->limit(20)
                ->get()
            : collect();

        return view('livewire.catalog.material-contributors', [
            'authors' => $authors,
            'topics' => $topics,
            'authorTypes' => AuthorType::query()->orderBy('type_name')->get(),
        ]);
    }
}
