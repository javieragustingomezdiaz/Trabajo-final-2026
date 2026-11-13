<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
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
use Illuminate\View\View;

class MaterialController extends Controller
{
    use AuthorizesRequests;

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
}
