<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelStaffReservationRequest;
use App\Http\Requests\SearchReservationsRequest;
use App\Http\Requests\StoreStaffReservationRequest;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationSearchService;
use App\Services\ReservationCancellationService;
use App\Services\ReservationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffReservationController extends Controller
{
    use AuthorizesRequests;

    public function index(
        SearchReservationsRequest $request,
        ReservationSearchService $searchService
    ): View {
        $filters = $request->filters();
        $hasCriteria = $request->hasCriteria();
        $searchAttempted = $request->query->count() > 0;
        $statuses = $searchService->statuses();
        $reservations = $hasCriteria ? $searchService->search($filters) : null;

        return view('reservations.staff-index', compact(
            'filters',
            'hasCriteria',
            'searchAttempted',
            'statuses',
            'reservations',
        ));
    }

    public function create(): View
    {
        $this->authorize('createForMember', Reservation::class);

        return view('reservations.staff-create');
    }

    public function store(
        StoreStaffReservationRequest $request,
        ReservationService $service
    ) {
        $member = User::query()->findOrFail($request->integer('member_id'));
        $material = Material::query()->findOrFail($request->integer('material_id'));

        $result = $service->store($member, $material, forStaff: true);
        $reservation = $result['reservation'];

        $message = $reservation->status?->name === Reservation::STATUS_WAITING
            ? 'Se agregó al socio a la cola de espera, en el puesto ' . $result['queue_position'] . '.'
            : 'La reserva quedó registrada para retiro hasta el '
                . $reservation->withdrawal_deadline->format('d/m/Y H:i') . '.';

        return redirect()
            ->route('staff-reservations.create')
            ->with('status', $message);
    }

    public function cancelForm(Reservation $reservation): View|RedirectResponse
    {
        $this->authorize('cancelForMember', $reservation);
        $reservation->load(['user.person', 'material.materialType', 'item', 'status']);

        if (! $reservation->isCancellable()) {
            return redirect()
                ->route('staff-reservations.index')
                ->withErrors(['reservation' => 'La reserva ya fue cancelada, venció o fue completada.']);
        }

        return view('reservations.staff-cancel', compact('reservation'));
    }

    public function cancel(
        CancelStaffReservationRequest $request,
        Reservation $reservation,
        ReservationCancellationService $cancellationService
    ): RedirectResponse {
        $cancelledReservation = $cancellationService->cancel(
            $reservation,
            $request->user(),
            trim($request->validated('cancellation_reason')),
        );

        return redirect()
            ->route('staff-reservations.index', [
                'alcance' => 'historial',
                'estado_id' => $cancelledReservation->reservation_status_id,
            ])
            ->with('status', 'La reserva del socio se canceló correctamente.');
    }
}
