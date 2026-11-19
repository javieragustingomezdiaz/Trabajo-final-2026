<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ReservationQueueService
{
    public function process(): int
    {
        $this->expireOffers();
        $notificationsSent = 0;

        $materialIds = Reservation::query()
            ->whereHas('status', fn ($query) => $query->where('name', Reservation::STATUS_WAITING))
            ->distinct()->pluck('material_id');

        foreach ($materialIds as $materialId) {
            while ($reservationId = $this->offerNext($materialId)) {
                if ($this->sendNotification($reservationId)) {
                    $notificationsSent++;
                } else {
                    break;
                }
            }
        }

        return $notificationsSent;
    }

    private function expireOffers(): void
    {
        $expiredIds = Reservation::query()
            ->whereNotNull('confirmation_expires_at')
            ->where('confirmation_expires_at', '<=', now())
            ->whereHas('status', fn ($query) => $query->where('name', Reservation::STATUS_NOTIFIED))
            ->pluck('id');

        foreach ($expiredIds as $id) {
            DB::transaction(function () use ($id): void {
                $reservation = Reservation::query()->with('status')->lockForUpdate()->find($id);
                if (! $reservation || $reservation->status?->name !== Reservation::STATUS_NOTIFIED
                    || ! $reservation->confirmation_expires_at?->isPast()) {
                    return;
                }

                $expired = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_EXPIRED]);
                $itemId = $reservation->item_id;
                $reservation->update([
                    'reservation_status_id' => $expired->id,
                    'item_id' => null,
                    'confirmation_token' => null,
                    'confirmation_expires_at' => null,
                ]);

                $item = Item::query()->with('status')->lockForUpdate()->find($itemId);
                if ($item && $item->status?->name === 'Reservado') {
                    $available = ItemStatus::firstOrCreate(['name' => 'Disponible']);
                    $item->update(['item_status_id' => $available->id]);
                }
            });
        }
    }

    private function offerNext(int $materialId): ?int
    {
        return DB::transaction(function () use ($materialId): ?int {
            $material = Material::query()->whereKey($materialId)->lockForUpdate()->first();
            if (! $material || ! $material->is_active) {
                return null;
            }

            $item = Item::query()->where('material_id', $materialId)
                ->where('is_active', true)
                ->whereHas('status', fn ($query) => $query->where('name', 'Disponible'))
                ->orderBy('id')->lockForUpdate()->first();

            $waiting = Reservation::query()
                ->where('material_id', $materialId)
                ->whereHas('status', fn ($query) => $query->where('name', Reservation::STATUS_WAITING))
                ->orderBy('registration_date')->orderBy('id')->lockForUpdate()->first();

            if (! $item || ! $waiting) {
                return null;
            }

            $notified = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_NOTIFIED]);
            $reserved = ItemStatus::firstOrCreate(['name' => 'Reservado']);
            $token = (string) Str::uuid();
            $now = now();

            $waiting->update([
                'reservation_status_id' => $notified->id,
                'item_id' => $item->id,
                'notified_at' => $now,
                'confirmation_expires_at' => $now->copy()->addHours(6),
                'confirmation_token' => $token,
            ]);
            $item->update(['item_status_id' => $reserved->id]);

            return $waiting->id;
        });
    }

    private function sendNotification(int $reservationId): bool
    {
        $reservation = Reservation::query()->with(['user.person', 'material'])->find($reservationId);
        $email = $reservation?->user?->person?->email;

        if (! $reservation || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::error('No se pudo notificar una reserva de la cola: el socio no tiene un correo válido.', [
                'reservation_id' => $reservationId,
            ]);
            return false;
        }

        $url = URL::temporarySignedRoute(
            'reservations.confirmation',
            $reservation->confirmation_expires_at,
            ['reservation' => $reservation->id, 'token' => $reservation->confirmation_token],
        );

        try {
            Mail::send('emails.reservations.ready', compact('reservation', 'url'), function ($message) use ($email, $reservation): void {
                $message->to($email)->subject('Hay un ejemplar disponible: ' . $reservation->material->title);
            });

            return true;
        } catch (\Throwable $exception) {
            Log::error('Falló el envío del aviso de reserva.', [
                'reservation_id' => $reservationId,
                'exception' => $exception->getMessage(),
            ]);
            $this->returnFailedOfferToQueue($reservationId);
            return false;
        }
    }

    private function returnFailedOfferToQueue(int $reservationId): void
    {
        DB::transaction(function () use ($reservationId): void {
            $reservation = Reservation::query()->with('status')->lockForUpdate()->find($reservationId);
            if (! $reservation || $reservation->status?->name !== Reservation::STATUS_NOTIFIED) {
                return;
            }

            $itemId = $reservation->item_id;
            $waiting = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_WAITING]);
            $reservation->update([
                'reservation_status_id' => $waiting->id,
                'item_id' => null,
                'notified_at' => null,
                'confirmation_expires_at' => null,
                'confirmation_token' => null,
            ]);

            $item = Item::query()->with('status')->lockForUpdate()->find($itemId);
            if ($item && $item->status?->name === 'Reservado') {
                $available = ItemStatus::firstOrCreate(['name' => 'Disponible']);
                $item->update(['item_status_id' => $available->id]);
            }
        });
    }
}
