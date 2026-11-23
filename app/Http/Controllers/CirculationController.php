<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCirculationRequest;
use App\Http\Requests\SearchCirculationsRequest;
use App\Http\Requests\RenewCirculationRequest;
use App\Http\Requests\ReturnCirculationRequest;
use App\Http\Requests\MarkCirculationLostRequest;
use App\Models\Circulation;
use App\Models\Item;
use App\Models\User;
use App\Services\CirculationService;
use App\Services\CirculationSearchService;
use App\Services\CirculationRenewalService;
use App\Services\CirculationReturnService;
use App\Services\CirculationLostService;
use App\Services\ReservationQueueService;
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

    public function renew(
        RenewCirculationRequest $request,
        Circulation $circulation,
        CirculationRenewalService $renewalService
    ): RedirectResponse {
        $renewed = $renewalService->renew($circulation, $request->user());

        return redirect()
            ->route('circulations.index', $request->query())
            ->with('status', 'Préstamo renovado. Nueva fecha estimada de devolución: '
                . $renewed->estimated_return_date->format('d/m/Y H:i') . '.');
    }

    public function recordReturn(
        ReturnCirculationRequest $request,
        Circulation $circulation,
        CirculationReturnService $returnService,
        ReservationQueueService $reservationQueueService
    ): RedirectResponse {
        $returned = $returnService->register(
            $circulation,
            $request->boolean('damaged'),
            $request->user(),
            $request->validated('return_note'),
        );

        $reservationQueueService->processForMaterial((int) $returned->item?->material_id);

        return redirect()
            ->route('circulations.index', $request->query())
            ->with('status', 'Devolución registrada correctamente. Estado: ' . $returned->status?->name . '.');
    }

    public function markLost(
        MarkCirculationLostRequest $request,
        Circulation $circulation,
        CirculationLostService $lostService,
        ReservationQueueService $reservationQueueService
    ): RedirectResponse {
        $lost = $lostService->markLost($circulation, $request->validated('reason'), $request->user());
        $materialId = (int) $lost->item?->material_id;
        $hasActiveCopies = Item::query()
            ->where('material_id', $materialId)
            ->where('is_active', true)
            ->exists();

        if ($hasActiveCopies) {
            $reservationQueueService->processForMaterial($materialId);
        } else {
            $reservationQueueService->notifyWaitingForUnavailableMaterial($materialId);
        }

        return redirect()
            ->route('circulations.index', $request->query())
            ->with('status', 'Préstamo registrado como perdido. El ejemplar se dio de baja y la cuenta del socio fue deshabilitada.');
    }
}
