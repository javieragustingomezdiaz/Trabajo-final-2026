<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCirculationRequest;
use App\Http\Requests\SearchCirculationsRequest;
use App\Models\Circulation;
use App\Models\Item;
use App\Models\User;
use App\Services\CirculationService;
use App\Services\CirculationSearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CirculationController extends Controller
{
    use AuthorizesRequests;

    public function create(): View
    {
        $this->authorize('create', Circulation::class);

        return view('circulations.create');
    }

    public function index(
        SearchCirculationsRequest $request,
        CirculationSearchService $searchService
    ): View {
        $filters = $request->filters();
        $hasCriteria = $request->hasCriteria();
        $searchAttempted = $request->searchWasSubmitted();
        $statuses = $searchService->statuses();
        $circulations = $hasCriteria ? $searchService->search($filters) : null;

        return view('circulations.index', compact(
            'filters',
            'hasCriteria',
            'searchAttempted',
            'statuses',
            'circulations',
        ));
    }

    public function store(StoreCirculationRequest $request, CirculationService $service): RedirectResponse
    {
        $validated = $request->validated();
        $member = User::query()->findOrFail($validated['member_id']);
        $item = Item::query()->findOrFail($validated['item_id']);

        $circulation = $service->register(
            $member,
            $item,
            $validated['loan_type'],
            $request->user(),
        );

        return redirect()
            ->route('circulations.create')
            ->with('status', 'Préstamo registrado. Fecha estimada de devolución: '
                . $circulation->estimated_return_date->format('d/m/Y H:i') . '.');
    }
}
