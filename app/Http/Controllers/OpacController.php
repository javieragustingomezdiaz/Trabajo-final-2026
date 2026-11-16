<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpacController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'author' => ['nullable', 'string', 'max:100'],
            'topic' => ['nullable', 'string', 'max:100'],
            'identifier' => ['nullable', 'string', 'max:100'],
            'material_type_id' => ['nullable', 'integer', 'exists:material_types,id'],
            'publisher' => ['nullable', 'string', 'max:100'],
            'publication_year' => ['nullable', 'integer', 'digits:4', 'between:1000,9999'],
        ]);

        foreach ($filters as $key => $value) {
            if (is_string($value)) {
                $filters[$key] = trim($value);
            }
        }

        $hasCriteria = collect($filters)->contains(fn ($value) => $value !== null && $value !== '');
        $searchAttempted = $request->query->count() > 0;

        $materials = Material::query()
            ->active()
            ->with(['authors', 'materialType', 'carrierType'])
            ->withCount([
                'items as active_copies_count' => fn ($query) => $query->where('is_active', true),
                'items as available_copies_count' => fn ($query) => $query
                    ->where('is_active', true)
                    ->whereHas('status', fn ($statusQuery) => $statusQuery->where('name', 'Disponible')),
            ])
            ->when(! $hasCriteria, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(($filters['title'] ?? '') !== '', fn ($query) =>
                $query->where('title', 'like', "%{$filters['title']}%"))
            ->when(($filters['author'] ?? '') !== '', fn ($query) =>
                $query->whereHas('authors', fn ($authors) => $authors->where('name', 'like', "%{$filters['author']}%")))
            ->when(($filters['topic'] ?? '') !== '', fn ($query) =>
                $query->whereHas('topics', fn ($topics) => $topics->where('name', 'like', "%{$filters['topic']}%")))
            ->when(($filters['identifier'] ?? '') !== '', fn ($query) =>
                $query->where('identifier_value', 'like', "%{$filters['identifier']}%"))
            ->when($filters['material_type_id'] ?? null, fn ($query, $id) =>
                $query->where('material_type_id', $id))
            ->when(($filters['publisher'] ?? '') !== '', fn ($query) =>
                $query->whereHas('publisher', fn ($publishers) => $publishers->where('name', 'like', "%{$filters['publisher']}%")))
            ->when($filters['publication_year'] ?? null, fn ($query, $year) =>
                $query->whereYear('publication_date', $year))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $materialTypes = MaterialType::query()->orderBy('name')->get();

        return view('opac.index', compact(
            'filters',
            'hasCriteria',
            'searchAttempted',
            'materials',
            'materialTypes',
        ));
    }

    public function show(Material $material): View
    {
        abort_unless($material->is_active, 404);

        $material->load([
            'authors',
            'topics',
            'publisher',
            'identifierType',
            'materialType',
            'language',
            'mediaType',
            'contentType',
            'carrierType',
            'items' => fn ($query) => $query
                ->where('is_active', true)
                ->with(['status', 'container.location'])
                ->orderBy('barcode'),
        ]);

        return view('opac.show', compact('material'));
    }
}
