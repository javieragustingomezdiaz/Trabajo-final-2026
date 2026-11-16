<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemRequest;
use App\Models\Container;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\ItemStatus;
use App\Models\Material;
use App\Services\ItemCatalogService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Location;

class ItemController extends Controller
{
    use AuthorizesRequests;

    public function create(Request $request): View
    {
        $this->authorize('viewAny', Item::class);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $term = trim($filters['q'] ?? '');

        $materials = Material::query()
            ->active()
            ->with(['authors', 'materialType'])
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('identifier_value', 'like', "%{$term}%")
                        ->orWhereHas('authors', fn ($authors) =>
                            $authors->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('items.select-material', compact('materials', 'filters'));
    }

    public function form(Material $material): View
    {
        $this->authorize('create', Item::class);

        abort_unless($material->is_active, 404);

        $material->load(['authors', 'materialType']);

        $statuses = ItemStatus::query()->orderBy('name')->get();
        $conditions = ItemCondition::query()->orderBy('name')->get();

        $containers = Container::query()
            ->with('location')
            ->orderBy('location_id')
            ->orderBy('name')
            ->get();

        return view('items.create', compact(
            'material',
            'statuses',
            'conditions',
            'containers',
        ));
    }

    public function store(
        StoreItemRequest $request,
        Material $material,
        ItemCatalogService $service
    ) {
        $this->authorize('create', Item::class);

        abort_unless($material->is_active, 404);

        $item = $service->store($material, $request->validated());

        return redirect()
            ->route('items.search')
            ->with('success', "Ejemplar registrado. Código de barras: {$item->barcode}");
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        $item->load([
            'material.authors',
            'material.materialType',
            'container.location',
            'status',
            'condition',
        ]);

        $statuses = ItemStatus::query()->orderBy('name')->get();
        $conditions = ItemCondition::query()->orderBy('name')->get();
        $containers = Container::query()
            ->with('location')
            ->orderBy('location_id')
            ->orderBy('name')
            ->get();

        return view('items.edit', compact(
            'item',
            'statuses',
            'conditions',
            'containers',
        ));
    }

    public function update(
        UpdateItemRequest $request,
        Item $item,
        ItemCatalogService $service
    ): RedirectResponse {
        $this->authorize('update', $item);

        $service->update($item, $request->validated());

        return redirect()
            ->route('items.search')
            ->with('success', "Ejemplar {$item->barcode} actualizado correctamente.");
    }

    public function search(Request $request): View
    {
        $this->authorize('viewAny', Item::class);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive,all'],
            'item_status_id' => ['nullable', 'integer', 'exists:item_statuses,id'],
            'item_condition_id' => ['nullable', 'integer', 'exists:item_conditions,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'container_id' => ['nullable', 'integer', 'exists:containers,id'],
        ]);

        $term = trim($filters['q'] ?? '');
        $status = $filters['status'] ?? 'active';

        $items = Item::query()
            ->with([
                'material.authors',
                'container.location',
                'status',
                'condition',
            ])
            ->when($term !== '', function ($query) use ($term): void {
                $like = "%{$term}%";

                $query->where(function ($query) use ($like): void {
                    $query->where('barcode', 'like', $like)
                        ->orWhere('observation', 'like', $like)
                        ->orWhereHas('material', function ($materialQuery) use ($like): void {
                            $materialQuery->where('title', 'like', $like)
                                ->orWhere('identifier_value', 'like', $like)
                                ->orWhereHas('authors', fn ($authorQuery) =>
                                    $authorQuery->where('name', 'like', $like));
                        })
                        ->orWhereHas('container', function ($containerQuery) use ($like): void {
                            $containerQuery->where('name', 'like', $like)
                                ->orWhereHas('location', fn ($locationQuery) =>
                                    $locationQuery->where('name', 'like', $like));
                        });
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when(
                $filters['item_status_id'] ?? null,
                fn ($query, $id) => $query->where('item_status_id', $id)
            )
            ->when(
                $filters['item_condition_id'] ?? null,
                fn ($query, $id) => $query->where('item_condition_id', $id)
            )
            ->when(
                $filters['container_id'] ?? null,
                fn ($query, $id) => $query->where('container_id', $id)
            )
            ->when(
                $filters['location_id'] ?? null,
                fn ($query, $id) => $query->whereHas(
                    'container',
                    fn ($containerQuery) => $containerQuery->where('location_id', $id)
                )
            )
            ->orderBy('barcode')
            ->paginate(15)
            ->withQueryString();

        $statuses = ItemStatus::query()->orderBy('name')->get();
        $conditions = ItemCondition::query()->orderBy('name')->get();

        $locations = Location::query()
            ->orderBy('name')
            ->get();

        $containers = Container::query()
            ->with('location')
            ->when(
                $filters['location_id'] ?? null,
                fn ($query, $id) => $query->where('location_id', $id)
            )
            ->orderBy('name')
            ->get();

        return view('items.search', compact(
            'items',
            'filters',
            'statuses',
            'conditions',
            'locations',
            'containers',
        ));
    }

    public function deactivate(Item $item): RedirectResponse
    {
        $this->authorize('deactivate', $item);

        if ($item->is_active) {
            $item->update(['is_active' => false]);
        }

        return redirect()
            ->route('items.search', ['status' => 'inactive'])
            ->with('success', "Ejemplar {$item->barcode} desactivado. Se conservaron sus datos e historial.");
    }

    public function activate(Item $item): RedirectResponse
    {
        $this->authorize('activate', $item);

        if (! $item->is_active) {
            $item->update(['is_active' => true]);
        }

        return redirect()
            ->route('items.search', ['status' => 'active'])
            ->with('success', "Ejemplar {$item->barcode} activado correctamente.");
    }
}
