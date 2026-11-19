<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        return view('reservations.confirmation', compact('reservation', 'token'));
    }

    public function confirm(Request $request, Reservation $reservation, string $token, ReservationService $service): RedirectResponse
    {
        $request->validate(['confirm' => ['required', 'accepted']]);
        $confirmed = $service->confirmWaitlistReservation($reservation, $token);

        return redirect()->route('opac.materials.show', $confirmed->material_id)
            ->with('status', 'Reserva confirmada. Podés retirar el ejemplar hasta el ' . $confirmed->withdrawal_deadline->format('d/m/Y H:i') . '.');
    }

    public function cancelOwn(Reservation $reservation): RedirectResponse
    {
        $this->authorize('cancelOwn', $reservation);

        DB::transaction(function () use ($reservation): void {
            $reservation = Reservation::query()
                ->with('status')
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->authorize('cancelOwn', $reservation);

            $activeStatuses = [
                Reservation::STATUS_WAITING,
                Reservation::STATUS_NOTIFIED,
                Reservation::STATUS_READY,
            ];

            if (! in_array($reservation->status?->name, $activeStatuses, true)) {
                throw ValidationException::withMessages([
                    'reservation' => 'Esta reserva ya no está activa y no se puede cancelar.',
                ]);
            }

            $itemId = $reservation->item_id;
            $cancelledStatus = ReservationStatus::firstOrCreate([
                'name' => Reservation::STATUS_CANCELLED,
            ]);

            $reservation->update([
                'reservation_status_id' => $cancelledStatus->id,
                'cancelled_at' => now(),
                'confirmation_token' => null,
                'confirmation_expires_at' => null,
            ]);

            if (! $itemId) {
                return;
            }

            $item = Item::query()
                ->with('status')
                ->whereKey($itemId)
                ->lockForUpdate()
                ->first();

            if (
                $item
                && $item->is_active
                && in_array($item->status?->name, ['Reservado', 'Reservado para retiro'], true)
            ) {
                $availableStatus = ItemStatus::firstOrCreate(['name' => 'Disponible']);
                $item->update(['item_status_id' => $availableStatus->id]);
            }
        });

        return redirect()
            ->route('reservations.mine', ['vista' => 'activas'])
            ->with('status', 'La reserva se canceló correctamente.');
    }
}
