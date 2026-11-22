<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Reservation;
use App\Services\ReservationCancellationService;
use App\Services\ReservationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function create(Material $material, ReservationService $service): View
    {
        $this->authorize('create', Reservation::class);
        abort_unless($material->is_active, 404);

        $material->load(['authors', 'materialType', 'items' => fn ($query) => $query
            ->where('is_active', true)->with(['status', 'container.location'])]);

        $preview = $service->preview(request()->user(), $material);

        return view('reservations.create', compact('material', 'preview'));
    }

    public function store(Request $request, Material $material, ReservationService $service): RedirectResponse
    {
        $this->authorize('create', Reservation::class);
        $request->validate(['confirm' => ['required', 'accepted']]);

        $result = $service->store($request->user(), $material);
        $reservation = $result['reservation'];

        if ($reservation->status?->name === Reservation::STATUS_WAITING) {
            return redirect()->route('opac.materials.show', $material)
                ->with('status', 'Te agregamos a la cola de espera en el puesto ' . $result['queue_position'] . '. Te avisaremos por correo cuando haya un ejemplar disponible.');
        }

        return redirect()->route('opac.materials.show', $material)
            ->with('status', 'La reserva quedó registrada. Podés retirar el ejemplar hasta el ' . $reservation->withdrawal_deadline->format('d/m/Y H:i') . '.');
    }

    public function confirmation(Reservation $reservation, string $token): View
    {
        abort_unless(
            $reservation->status?->name === Reservation::STATUS_NOTIFIED
                && $reservation->confirmation_token
                && hash_equals($reservation->confirmation_token, $token)
                && $reservation->confirmation_expires_at?->isFuture(),
            410,
        );

        $reservation->load(['material.authors', 'material.materialType', 'item.container.location']);

        $expiresAt = $reservation->confirmation_expires_at;
        $routeParameters = ['reservation' => $reservation->id, 'token' => $token];
        $confirmUrl = URL::temporarySignedRoute('reservations.confirm', $expiresAt, $routeParameters);
        $rejectUrl = URL::temporarySignedRoute('reservations.reject', $expiresAt, $routeParameters);

        return view('reservations.confirmation', compact('reservation', 'confirmUrl', 'rejectUrl'));
    }

    public function confirm(Request $request, Reservation $reservation, string $token, ReservationService $service): RedirectResponse
    {
        $request->validate(['confirm' => ['required', 'accepted']]);
        $confirmed = $service->confirmWaitlistReservation($reservation, $token);

        return redirect()->route('opac.materials.show', $confirmed->material_id)
            ->with('status', 'Reserva confirmada. Podés retirar el ejemplar hasta el ' . $confirmed->withdrawal_deadline->format('d/m/Y H:i') . '.');
    }

    public function reject(Request $request, Reservation $reservation, string $token, ReservationService $service): RedirectResponse
    {
        $request->validate(['reject' => ['required', 'accepted']]);
        $rejected = $service->rejectWaitlistReservation($reservation, $token);

        return redirect()->route('opac.materials.show', $rejected->material_id)
            ->with('status', 'Rechazaste la oferta. El ejemplar se ofrecerá al siguiente socio de la cola.');
    }

    public function cancelOwn(
        Reservation $reservation,
        Request $request,
        ReservationCancellationService $cancellationService
    ): RedirectResponse
    {
        $this->authorize('cancelOwn', $reservation);
        $cancellationService->cancel($reservation, $request->user());

        return redirect()
            ->route('reservations.mine', ['vista' => 'activas'])
            ->with('status', 'La reserva se canceló correctamente.');
    }
}
