<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationCancellationService
{
    public function __construct(
        private readonly ReservedItemReleaseService $reservedItemReleaseService
    ) {
    }

    public function cancel(
        Reservation $reservation,
        User $actor,
        ?string $reason = null
    ): Reservation {
        return DB::transaction(function () use ($reservation, $actor, $reason): Reservation {
            $lockedReservation = Reservation::query()
                ->with('status')
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedReservation->isCancellable()) {
                throw ValidationException::withMessages([
                    'reservation' => 'Esta reserva ya no está activa y no se puede cancelar.',
                ]);
            }

            $itemId = $lockedReservation->item_id;
            $cancelledStatus = ReservationStatus::firstOrCreate([
                'name' => Reservation::STATUS_CANCELLED,
            ]);

            $lockedReservation->update([
                'reservation_status_id' => $cancelledStatus->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'cancelled_by_user_id' => $actor->id,
                'confirmation_token' => null,
                'confirmation_expires_at' => null,
            ]);

            $this->reservedItemReleaseService->release($itemId);

            return $lockedReservation->refresh()->load(['status', 'item']);
        });
    }

}
