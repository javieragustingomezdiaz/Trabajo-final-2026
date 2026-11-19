<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffReservationRequest;
use App\Models\Reservation;
use App\Models\Material;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class StaffReservationController extends Controller
{
    use AuthorizesRequests;

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
}
