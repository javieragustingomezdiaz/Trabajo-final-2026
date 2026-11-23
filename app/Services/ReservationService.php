<?php

namespace App\Services;

use App\Models\Circulation;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\LoanTerm;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public const QUEUE_LIMIT = 5;

    private const DEFAULT_PICKUP_HOURS = 24;

    public function __construct(
        private readonly ReservedItemReleaseService $reservedItemReleaseService,
        private readonly GlobalRuleService $globalRuleService
    ) {
    }

    public function preview(User $user, Material $material, bool $forStaff = false): array
    {
        $this->assertEligible($user, $material, $forStaff);

        $queueCount = $this->queueQuery($material)->count();
        $item = $queueCount === 0
            ? $this->availableItems($material)->with('container.location')->first()
            : null;

        if ($item) {
            $pickupDeadline = now()->addHours($this->pickupHours($material));

            return [
                'immediate' => true,
                'item' => $item,
                'queue_position' => null,
                'pickup_deadline' => $pickupDeadline,
            ];
        }

        $this->assertQueueHasSpace($queueCount);

        return [
            'immediate' => false,
            'item' => null,
            'queue_position' => $queueCount + 1,
            'pickup_deadline' => null,
        ];
    }

    public function store(User $user, Material $material, bool $forStaff = false): array
    {
        return DB::transaction(function () use ($user, $material, $forStaff): array {
            $lockedMaterial = Material::query()
                ->whereKey($material->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertEligible($user, $lockedMaterial, $forStaff);

            $queueCount = $this->queueQuery($lockedMaterial)->count();
            $item = $queueCount === 0
                ? $this->availableItems($lockedMaterial)->lockForUpdate()->first()
                : null;

            $registeredAt = now();

            if ($item) {
                $status = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_READY]);
                $readyItemStatus = ItemStatus::firstOrCreate(['name' => 'Reservado para retiro']);
                $pickupDeadline = $registeredAt->copy()->addHours($this->pickupHours($lockedMaterial));

                $reservation = Reservation::create([
                    'registration_date' => $registeredAt,
                    'withdrawal_deadline' => $pickupDeadline,
                    'reservation_status_id' => $status->id,
                    'material_id' => $lockedMaterial->id,
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                ]);

                $item->update(['item_status_id' => $readyItemStatus->id]);

                return [
                    'reservation' => $reservation->load('status', 'item.container.location'),
                    'queue_position' => null,
                ];
            }

            $this->assertQueueHasSpace($queueCount);
            $status = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_WAITING]);

            $reservation = Reservation::create([
                'registration_date' => $registeredAt,
                'withdrawal_deadline' => null,
                'reservation_status_id' => $status->id,
                'material_id' => $lockedMaterial->id,
                'item_id' => null,
                'user_id' => $user->id,
            ]);

            return [
                'reservation' => $reservation->load('status'),
                'queue_position' => $queueCount + 1,
            ];
        });
    }

    public function confirmWaitlistReservation(Reservation $reservation, string $token): Reservation
    {
        return DB::transaction(function () use ($reservation, $token): Reservation {
            $lockedReservation = $this->lockValidOffer($reservation, $token);
            $item = $this->lockOfferedItem($lockedReservation);

            $readyStatus = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_READY]);
            $pickupStatus = ItemStatus::firstOrCreate(['name' => 'Reservado para retiro']);
            $withdrawalDeadline = now()->addHours($this->pickupHours($lockedReservation->material));

            $lockedReservation->update([
                'reservation_status_id' => $readyStatus->id,
                'withdrawal_deadline' => $withdrawalDeadline,
                'confirmation_expires_at' => null,
                'confirmation_token' => null,
            ]);
            $item->update(['item_status_id' => $pickupStatus->id]);

            return $lockedReservation->refresh()->load('status', 'material');
        });
    }

    public function rejectWaitlistReservation(Reservation $reservation, string $token): Reservation
    {
        return DB::transaction(function () use ($reservation, $token): Reservation {
            $lockedReservation = $this->lockValidOffer($reservation, $token);
            $item = $this->lockOfferedItem($lockedReservation);
            $rejectedStatus = ReservationStatus::firstOrCreate(['name' => Reservation::STATUS_REJECTED]);

            $lockedReservation->update([
                'reservation_status_id' => $rejectedStatus->id,
                'confirmation_expires_at' => null,
                'confirmation_token' => null,
            ]);

            $this->reservedItemReleaseService->release($item->id);

            return $lockedReservation->refresh()->load('status', 'material', 'item');
        });
    }

    private function lockValidOffer(Reservation $reservation, string $token): Reservation
    {
        $lockedReservation = Reservation::query()
            ->with(['material.materialType.loanTerm', 'status'])
            ->whereKey($reservation->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        if (
            $lockedReservation->status?->name !== Reservation::STATUS_NOTIFIED
            || ! $lockedReservation->confirmation_token
            || ! hash_equals($lockedReservation->confirmation_token, $token)
            || ! $lockedReservation->confirmation_expires_at
            || $lockedReservation->confirmation_expires_at->isPast()
        ) {
            throw ValidationException::withMessages([
                'reservation' => 'La oferta ya venció o el enlace de confirmación no es válido.',
            ]);
        }

        return $lockedReservation;
    }

    private function lockOfferedItem(Reservation $reservation): Item
    {
        $item = Item::query()
            ->with('status')
            ->whereKey($reservation->item_id)
            ->lockForUpdate()
            ->first();

        if (! $item || ! $item->is_active || $item->status?->name !== 'Reservado') {
            throw ValidationException::withMessages([
                'reservation' => 'El ejemplar asignado ya no está disponible para esta oferta.',
            ]);
        }

        return $item;
    }

    public function pickupHours(Material $material): int
    {
        return max(1, (int) ($material->materialType?->loanTerm?->hours_on_standby ?? self::DEFAULT_PICKUP_HOURS));
    }

    public function availableItems(Material $material)
    {
        return Item::query()
            ->where('material_id', $material->id)
            ->where('is_active', true)
            ->whereHas('status', fn ($query) => $query->where('name', 'Disponible'))
            ->orderBy('id');
    }

    private function assertEligible(User $user, Material $material, bool $forStaff = false): void
    {
        if (! $user->is_active || ! $user->role?->is_active || $user->role?->name !== 'member') {
            throw ValidationException::withMessages([
                'reservation' => 'Solo los socios con una cuenta activa pueden reservar materiales.',
            ]);
        }

        if (! $material->is_active) {
            throw ValidationException::withMessages([
                'reservation' => 'Este material no está disponible para reservar.',
            ]);
        }

        $loanTerm = LoanTerm::query()
            ->where('material_type_id', $material->material_type_id)
            ->first();

        if ($loanTerm && ! $loanTerm->bookable) {
            throw ValidationException::withMessages([
                'reservation' => 'Este tipo de material no permite reservas.',
            ]);
        }

        $now = now();
        $hasActiveSanction = Sanction::query()
            ->where('user_id', $user->id)
            ->whereNull('lifted_on')
            ->where('start_on', '<=', $now)
            ->where(fn ($query) => $query
                ->whereNull('end_on')
                ->orWhere('end_on', '>=', $now))
            ->exists();

        if ($hasActiveSanction) {
            throw ValidationException::withMessages([
                'reservation' => $forStaff
                    ? 'El socio tiene sanciones vigentes y no puede reservar un material.'
                    : 'No podés reservar mientras tengas una sanción vigente.',
            ]);
        }

        $hasOverdueLoan = Circulation::query()
            ->active()
            ->where('user_id', $user->id)
            ->where(fn ($query) => $query
                ->where('estimated_return_date', '<', $now)
                ->orWhereHas('status', fn ($status) => $status->where('name', Circulation::STATUS_OVERDUE)))
            ->exists();

        if ($hasOverdueLoan) {
            throw ValidationException::withMessages([
                'reservation' => $forStaff
                    ? 'El socio tiene préstamos demorados y no puede reservar materiales.'
                    : 'Tenés préstamos demorados y no podés reservar materiales.',
            ]);
        }

        $activeReservationStatuses = [
            Reservation::STATUS_WAITING,
            Reservation::STATUS_NOTIFIED,
            Reservation::STATUS_READY,
        ];

        $alreadyReservedThisTitle = Reservation::query()
            ->where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->whereHas('status', fn ($query) => $query->whereIn('name', $activeReservationStatuses))
            ->exists();

        if ($alreadyReservedThisTitle) {
            throw ValidationException::withMessages([
                'reservation' => $forStaff
                    ? 'El socio ya posee una reserva activa de este material.'
                    : 'Ya tenés una reserva activa para este título.',
            ]);
        }

        $alreadyLoanedThisTitle = Circulation::query()
            ->active()
            ->where('user_id', $user->id)
            ->whereHas('item', fn ($itemQuery) => $itemQuery->where('material_id', $material->id))
            ->exists();

        if ($alreadyLoanedThisTitle) {
            throw ValidationException::withMessages([
                'reservation' => $forStaff
                    ? 'El socio ya posee un préstamo activo de este material.'
                    : 'Ya tenés un préstamo activo de este título.',
            ]);
        }

        $globalRule = $this->globalRuleService->current();
        $reservationsForPatron = Reservation::query()
            ->where('user_id', $user->id)
            ->whereHas('status', fn ($query) => $query->whereIn('name', Reservation::ACTIVE_STATUSES))
            ->count();

        if ($reservationsForPatron >= $globalRule->max_simultaneous_reservations) {
            throw ValidationException::withMessages([
                'reservation' => $forStaff
                    ? 'El socio alcanzó el máximo global de reservas simultáneas permitido.'
                    : 'Alcanzaste el máximo global de reservas simultáneas permitido.',
            ]);
        }

        if (! $loanTerm) {
            return;
        }

        if ($loanTerm->max_simultaneous_reservations !== null) {
            $reservationsForType = Reservation::query()
                ->where('user_id', $user->id)
                ->whereHas('status', fn ($query) => $query->whereIn('name', $activeReservationStatuses))
                ->whereHas('material', fn ($query) => $query->where('material_type_id', $material->material_type_id))
                ->count();

            if ($reservationsForType >= $loanTerm->max_simultaneous_reservations) {
                throw ValidationException::withMessages([
                    'reservation' => $forStaff
                        ? 'El socio alcanzó el máximo de reservas simultáneas permitido para este tipo de material.'
                        : 'Alcanzaste el máximo de reservas simultáneas permitido para este tipo de material.',
                ]);
            }
        }

    }

    private function queueQuery(Material $material)
    {
        return Reservation::query()
            ->where('material_id', $material->id)
            ->whereHas('status', fn ($query) => $query->whereIn('name', [
                Reservation::STATUS_WAITING,
                Reservation::STATUS_NOTIFIED,
            ]));
    }

    private function assertQueueHasSpace(int $queueCount): void
    {
        if ($queueCount >= self::QUEUE_LIMIT) {
            throw ValidationException::withMessages([
                'reservation' => 'La cola de espera de este título alcanzó el máximo de cinco socios.',
            ]);
        }
    }
}
