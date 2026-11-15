<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\CarrierType;
use App\Models\ContentType;
use App\Models\IdentifierType;
use App\Models\Language;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\MediaType;
use App\Models\Publisher;
use App\Services\MaterialCatalogService;
use App\Services\MaterialSpecializationRegistry;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialController extends Controller
{
    use AuthorizesRequests;

    public function search(Request $request): View
    {
        $this->authorize('viewAny', Material::class);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'material_type_id' => ['nullable', 'integer', 'exists:material_types,id'],
            'status' => ['nullable', 'in:active,inactive,all'],
        ]);

        $term = trim($filters['q'] ?? '');
$status = $filters['status'] ?? 'active';

        $materials = Material::query()
            ->with(['publisher', 'materialType', 'identifierType', 'authors', 'topics'])
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('identifier_value', 'like', "%{$term}%")
                        ->orWhereHas('publisher', fn ($publisherQuery) =>
                            $publisherQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('authors', fn ($authorQuery) =>
                            $authorQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('topics', fn ($topicQuery) =>
                            $topicQuery->where('name', 'like', "%{$term}%"));
                });
            })
            ->when(
                $filters['material_type_id'] ?? null,
                fn ($query, $typeId) => $query->where('material_type_id', $typeId)
            )
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        $materialTypes = MaterialType::query()
            ->orderBy('name')
            ->get();

        return view('materials.search', compact('materials', 'materialTypes', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('create', Material::class);

        $materialTypes = MaterialType::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        return view('materials.select-type', compact('materialTypes'));
    }

    public function form(
        MaterialType $materialType,
        MaterialSpecializationRegistry $registry
    ): View {
        $this->authorize('create', Material::class);

        $specialization = $registry->forType($materialType);

        $publishers = Publisher::query()->orderBy('name')->get();
        $identifierTypes = IdentifierType::query()->orderBy('name')->get();
        $languages = Language::query()->orderBy('name')->get();
        $mediaTypes = MediaType::query()->orderBy('term')->get();
        $contentTypes = ContentType::query()->orderBy('term')->get();
        $carrierTypes = CarrierType::query()->orderBy('term')->get();

        return view('materials.create', compact(
            'materialType',
            'specialization',
            'publishers',
            'identifierTypes',
            'languages',
            'mediaTypes',
            'contentTypes',
            'carrierTypes',
        ));
    }

    public function edit(
        Material $material,
        MaterialSpecializationRegistry $registry
    ): View {
        $this->authorize('update', $material);

        $material->load([
            'materialType',
            'authors',
            'topics',
            'bibliographic',
            'serialpublication',
            'cartographic',
            'graph',
            'musical',
            'sound',
            'video',
            'object',
        ]);

        $materialType = $material->materialType;
        $specialization = $registry->forType($materialType);
        $publishers = Publisher::query()->orderBy('name')->get();
        $identifierTypes = IdentifierType::query()->orderBy('name')->get();
        $languages = Language::query()->orderBy('name')->get();
        $mediaTypes = MediaType::query()->orderBy('term')->get();
        $contentTypes = ContentType::query()->orderBy('term')->get();
        $carrierTypes = CarrierType::query()->orderBy('term')->get();

        return view('materials.create', compact(
            'material',
            'materialType',
            'specialization',
            'publishers',
            'identifierTypes',
            'languages',
            'mediaTypes',
            'contentTypes',
            'carrierTypes',
        ));
    }

    public function store(
        StoreMaterialRequest $request,
        MaterialType $materialType,
        MaterialSpecializationRegistry $registry,
        MaterialCatalogService $service
    ) {
        $specialization = $registry->forType($materialType);

        $service->store(
            $request->validated(),
            $materialType,
            $specialization
        );

        return redirect()
            ->route('materials.create')
            ->with('success', 'Material registrado correctamente.');
    }

    public function update(
        UpdateMaterialRequest $request,
        Material $material,
        MaterialSpecializationRegistry $registry,
        MaterialCatalogService $service
    ) {
        $this->authorize('update', $material);

        $service->update(
            $material,
            $request->validated(),
            $registry->forType($material->materialType)
        );

        return redirect()
            ->route('materials.search')
            ->with('success', 'Material actualizado correctamente.');
    }

    public function deactivate(Material $material)
    {
        $this->authorize('deactivate', $material);

        if ($material->is_active) {
            $material->update(['is_active' => false]);
        }

        return redirect()
            ->route('materials.search', ['status' => 'inactive'])
            ->with('success', 'Material desactivado. Sus ejemplares y movimientos de circulación no se modificaron.');
    }

    public function activate(Material $material)
    {
        $this->authorize('activate', $material);

        if (! $material->is_active) {
            $material->update(['is_active' => true]);
        }

        return redirect()
            ->route('materials.search', ['status' => 'active'])
            ->with('success', 'Material activado correctamente.');
    }
}
