<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContainerRequest;
use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Location::class);

        $locations = Location::query()
            ->with('containers')
            ->orderBy('name')
            ->paginate(15);

        return view('locations.index', compact('locations'));
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::query()->create($request->validated());

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación registrada correctamente.');
    }

    public function storeContainer(
        StoreContainerRequest $request,
        Location $location
    ): RedirectResponse {
        $location->containers()->create([
            'name' => $request->validated('container_name'),
        ]);

        return redirect()
            ->route('locations.index')
            ->with('success', "Contenedor agregado a «{$location->name}».");
    }
}
