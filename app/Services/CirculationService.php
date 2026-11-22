<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Circulation;
use App\Models\GlobalRule;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\LoanStatus;
use App\Models\LoanTerm;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirculationService
{
    public function __construct(private readonly GlobalRuleService $globalRuleService)
    {
    }

    public function register(User $member, Item $item, string $loanType, User $actor): Circulation
    {
        return DB::transaction(function () use ($member, $item, $loanType, $actor): Circulation {
            $member = User::query()
                ->with('role')
                ->whereKey($member->id)
                ->lockForUpdate()
                ->firstOrFail();
            $pickupReservation = $this->pickupReservation($member, $item->id);
            $item = Item::query()
                ->with(['status', 'material.materialType.loanTerm'])
                ->whereKey($item->id)
                ->lockForUpdate()
                ->firstOrFail();

            $now = now();
            $this->ensureEligibleMember($member);
            $this->ensureEligibleItem($item);

            $material = $item->material;
            $loanTerm = $material?->materialType?->loanTerm;

            if (! $loanTerm) {
                throw ValidationException::withMessages([
                    'item_id' => 'El tipo de material no tiene una regla de préstamo configurada.',
                ]);
            }

            $this->ensureLoanTypeAllowed($loanTerm, $loanType);
            $this->ensureNoActiveSanction($member);
            $this->ensureNoOverdueLoans($member);

            $this->ensureNoDuplicateMaterialActivity($member, $material->id, $pickupReservation);

            $this->ensureItemCanBeCheckedOut($item, $pickupReservation);
            $this->ensureItemHasNoActiveLoan($item);

            $globalRule = $this->globalRuleService->current();
            $this->ensureLoanLimits($member, $material->material_type_id, $loanTerm, $globalRule);

            $dueAt = $this->dueDate($loanType, $loanTerm, $globalRule, $now);
            $activeStatus = LoanStatus::firstOrCreate(['name' => Circulation::STATUS_ACTIVE]);
            $checkedOutStatus = ItemStatus::firstOrCreate(['name' => 'Prestado']);

            $circulation = Circulation::create([
                'loan_type' => $loanType,
                'loan_date' => $now,
                'estimated_return_date' => $dueAt,
                'number_renewals' => 0,
                'item_id' => $item->id,
                'reservation_id' => $pickupReservation?->id,
                'user_id' => $member->id,
                'loan_status_id' => $activeStatus->id,
            ]);

            $item->update(['item_status_id' => $checkedOutStatus->id]);

            if ($pickupReservation) {
                $pickedUpStatus = ReservationStatus::firstOrCreate([
                    'name' => Reservation::STATUS_PICKED_UP,
                ]);
                $pickupReservation->update([
                    'reservation_status_id' => $pickedUpStatus->id,
                    'withdrawal_date' => $now,
                ]);
            }

            $this->recordAudit($circulation, $member, $item, $actor, $now);

            return $circulation->refresh()->load([
                'status',
                'user.person',
                'item.material',
            ]);
        });
    }

    private function ensureEligibleMember(User $member): void
    {
        if (! $member->is_active || ! $member->role?->is_active || $member->role?->name !== 'member') {
            throw ValidationException::withMessages([
                'member_id' => 'El socio no tiene una cuenta activa habilitada para operar.',
            ]);
        }
    }

    private function ensureEligibleItem(Item $item): void
    {
        if (! $item->is_active || ! $item->material?->is_active) {
            throw ValidationException::withMessages([
                'item_id' => 'El ejemplar o su material están inactivos.',
            ]);
        }
    }

    private function ensureLoanTypeAllowed(LoanTerm $loanTerm, string $loanType): void
    {
        $allowed = match ($loanType) {
            Circulation::TYPE_HOME => $loanTerm->is_home_loanable,
            Circulation::TYPE_IN_LIBRARY => $loanTerm->allow_in_library_use,
            default => false,
        };

        if (! $allowed) {
            $message = $loanType === Circulation::TYPE_HOME
                ? 'Este tipo de material no permite préstamos a domicilio.'
                : 'Este tipo de material no permite consulta en sala.';

            throw ValidationException::withMessages(['loan_type' => $message]);
        }
    }

    private function ensureNoActiveSanction(User $member): void
    {
        $now = now();
        $hasActiveSanction = Sanction::query()
            ->where('user_id', $member->id)
            ->whereNull('lifted_on')
            ->where('start_on', '<=', $now)
            ->where(fn ($query) => $query
                ->whereNull('end_on')
                ->orWhere('end_on', '>=', $now))
            ->exists();

        if ($hasActiveSanction) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio posee una sanción activa y no puede solicitar préstamos.',
            ]);
        }
    }

    private function ensureNoOverdueLoans(User $member): void
    {
        $hasOverdueLoan = Circulation::query()
            ->where('user_id', $member->id)
            ->whereNull('actual_return_date')
            ->where(fn ($query) => $query
                ->where('estimated_return_date', '<', now())
                ->orWhereHas('status', fn ($status) => $status->where('name', Circulation::STATUS_OVERDUE)))
            ->exists();

        if ($hasOverdueLoan) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio tiene préstamos demorados y no puede solicitar nuevos préstamos.',
            ]);
        }
    }

    private function pickupReservation(User $member, int $itemId): ?Reservation
    {
        return Reservation::query()
            ->with('status')
            ->where('item_id', $itemId)
            ->where('user_id', $member->id)
            ->whereHas('status', fn ($query) => $query->where('name', Reservation::STATUS_READY))
            ->lockForUpdate()
            ->first();
    }

    private function ensureNoDuplicateMaterialActivity(
        User $member,
        int $materialId,
        ?Reservation $pickupReservation
    ): void {
        $hasOtherActiveReservation = Reservation::query()
            ->where('user_id', $member->id)
            ->where('material_id', $materialId)
            ->whereHas('status', fn ($query) => $query->whereIn('name', Reservation::ACTIVE_STATUSES))
            ->when($pickupReservation, fn ($query) => $query->whereKeyNot($pickupReservation->id))
            ->exists();

        $hasActiveLoan = Circulation::query()
            ->where('user_id', $member->id)
            ->whereNull('actual_return_date')
            ->whereHas('item', fn ($query) => $query->where('material_id', $materialId))
            ->exists();

        if ($hasOtherActiveReservation || $hasActiveLoan) {
            throw ValidationException::withMessages([
                'item_id' => 'El socio ya posee una reserva o un préstamo activo de este material.',
            ]);
        }
    }

    private function ensureItemCanBeCheckedOut(Item $item, ?Reservation $pickupReservation): void
    {
        $status = $item->status?->name;
        $isAvailable = $status === 'Disponible';
        $isReservedForThisMember = $status === 'Reservado para retiro'
            && $pickupReservation !== null
            && (int) $pickupReservation->material_id === (int) $item->material_id;

        if (! $isAvailable && ! $isReservedForThisMember) {
            throw ValidationException::withMessages([
                'item_id' => 'El ejemplar no está disponible para préstamo.',
            ]);
        }
    }

    private function ensureItemHasNoActiveLoan(Item $item): void
    {
        $hasActiveLoan = Circulation::query()
            ->where('item_id', $item->id)
            ->whereNull('actual_return_date')
            ->exists();

        if ($hasActiveLoan) {
            throw ValidationException::withMessages([
                'item_id' => 'El ejemplar ya tiene un préstamo activo.',
            ]);
        }
    }

    private function ensureLoanLimits(
        User $member,
        int $materialTypeId,
        LoanTerm $loanTerm,
        GlobalRule $globalRule
    ): void {
        $activeLoans = Circulation::query()
            ->where('user_id', $member->id)
            ->whereNull('actual_return_date');

        if ($activeLoans->count() >= $globalRule->max_simultaneous_loans) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio alcanzó el límite máximo de préstamos totales permitidos.',
            ]);
        }

        if ($loanTerm->max_simultaneous_loans !== null) {
            $loansForType = Circulation::query()
                ->where('user_id', $member->id)
                ->whereNull('actual_return_date')
                ->whereHas('item.material', fn ($query) => $query->where('material_type_id', $materialTypeId))
                ->count();

            if ($loansForType >= $loanTerm->max_simultaneous_loans) {
                throw ValidationException::withMessages([
                    'member_id' => 'El socio alcanzó el límite máximo de préstamos para este tipo de material.',
                ]);
            }
        }
    }

    private function dueDate(string $loanType, LoanTerm $loanTerm, GlobalRule $globalRule, \Illuminate\Support\Carbon $now): \Illuminate\Support\Carbon
    {
        if ($loanType === Circulation::TYPE_HOME) {
            $term = $loanTerm->home_loan_days;
            $globalLimit = $globalRule->max_loan_days;
            $unit = 'días';
        } else {
            $term = $loanTerm->in_library_loan_hours;
            $globalLimit = $globalRule->max_consultation_hours;
            $unit = 'horas';
        }

        if (! $term || ! $globalLimit) {
            throw ValidationException::withMessages([
                'loan_type' => "La regla de préstamo no tiene una duración válida en {$unit}.",
            ]);
        }

        $duration = min((int) $term, (int) $globalLimit);

        return $loanType === Circulation::TYPE_HOME
            ? $now->copy()->addDays($duration)
            : $now->copy()->addHours($duration);
    }

    private function recordAudit(
        Circulation $circulation,
        User $member,
        Item $item,
        User $actor,
        \Illuminate\Support\Carbon $now
    ): void {
        $audit = Audit::create([
            'affected_model' => Circulation::class,
            'action' => 'Registrar préstamo',
            'afected_record' => (string) $circulation->id,
            'user_id' => $actor->id,
            'registration_date' => $now,
        ]);

        $audit->details()->createMany([
            ['field_name' => 'user_id', 'old_value' => null, 'new_value' => (string) $member->id],
            ['field_name' => 'item_id', 'old_value' => null, 'new_value' => (string) $item->id],
            ['field_name' => 'loan_type', 'old_value' => null, 'new_value' => $circulation->loan_type],
            ['field_name' => 'loan_date', 'old_value' => null, 'new_value' => $circulation->loan_date->toDateTimeString()],
            ['field_name' => 'estimated_return_date', 'old_value' => null, 'new_value' => $circulation->estimated_return_date->toDateTimeString()],
            ['field_name' => 'reservation_id', 'old_value' => null, 'new_value' => $circulation->reservation_id ? (string) $circulation->reservation_id : null],
        ]);
    }
}
