<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Circulation;
use App\Models\GlobalRule;
use App\Models\Item;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirculationRenewalService
{
    public function __construct(private readonly GlobalRuleService $globalRuleService)
    {
    }

    public function renew(Circulation $circulation, User $actor): Circulation
    {
        return DB::transaction(function () use ($circulation, $actor): Circulation {
            $loanSnapshot = Circulation::query()->findOrFail($circulation->id);
            $member = User::query()
                ->whereKey($loanSnapshot->user_id)
                ->lockForUpdate()
                ->firstOrFail();
            $materialId = Item::query()->whereKey($loanSnapshot->item_id)->value('material_id');

            Material::query()->whereKey($materialId)->lockForUpdate()->firstOrFail();

            $loan = Circulation::query()
                ->with('status')
                ->whereKey($circulation->id)
                ->lockForUpdate()
                ->firstOrFail();
            $item = Item::query()
                ->with(['status', 'material.materialType.loanTerm'])
                ->whereKey($loan->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            $globalRule = $this->globalRuleService->current();
            $renewalDays = $this->renewalDaysIfAllowed($loan, $member, $item, $globalRule);

            $oldDueDate = $loan->estimated_return_date;
            $newDueDate = $oldDueDate->copy()->addDays($renewalDays);
            $oldRenewalCount = $loan->number_renewals;

            $loan->update([
                'estimated_return_date' => $newDueDate,
                'number_renewals' => $oldRenewalCount + 1,
            ]);

            $this->recordAudit($loan, $actor, $oldDueDate, $newDueDate, $oldRenewalCount);

            return $loan->refresh()->load(['status', 'user.person', 'item.material']);
        });
    }

    public function canRenew(Circulation $circulation): bool
    {
        $circulation->loadMissing(['status', 'user.role', 'item.status', 'item.material.materialType.loanTerm']);

        try {
            $this->renewalDaysIfAllowed(
                $circulation,
                $circulation->user,
                $circulation->item,
                $this->globalRuleService->current()
            );

            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    private function renewalDaysIfAllowed(Circulation $loan, ?User $member, ?Item $item, GlobalRule $globalRule): int
    {
        $this->ensureRenewableLoan($loan);

        if (! $member || ! $item) {
            throw ValidationException::withMessages([
                'circulation' => 'No es posible renovar el préstamo de este ejemplar.',
            ]);
        }

        $this->ensureMemberCanRenew($member);
        $this->ensureItemCanBeRenewed($item);
        $this->ensureNoActiveSanction($member);
        $this->ensureNoPendingReservations($item);

        $loanTerm = $item->material?->materialType?->loanTerm;
        if (! $loanTerm || ! $loanTerm->is_renewable || $loan->loan_type === Circulation::TYPE_IN_LIBRARY) {
            throw ValidationException::withMessages([
                'circulation' => 'No es posible renovar el préstamo de este ejemplar.',
            ]);
        }

        $maxRenewals = min((int) $loanTerm->max_renewal, (int) $globalRule->max_renewals);
        if ($loan->number_renewals >= $maxRenewals) {
            throw ValidationException::withMessages([
                'circulation' => 'El socio alcanzó el límite de renovaciones permitido.',
            ]);
        }

        $renewalDays = min((int) $loanTerm->renewal_days, (int) $globalRule->max_loan_days);
        if ($renewalDays < 1) {
            throw ValidationException::withMessages([
                'circulation' => 'La regla no tiene una duración de renovación válida.',
            ]);
        }

        return $renewalDays;
    }

    private function ensureRenewableLoan(Circulation $loan): void
    {
        if (
            $loan->status?->name !== Circulation::STATUS_ACTIVE
            || $loan->actual_return_date !== null
            || ! $loan->estimated_return_date
            || $loan->estimated_return_date->isPast()
        ) {
            throw ValidationException::withMessages([
                'circulation' => 'Solo se pueden renovar préstamos activos y dentro del plazo.',
            ]);
        }
    }

    private function ensureMemberCanRenew(User $member): void
    {
        if (! $member->is_active || ! $member->role?->is_active || $member->role?->name !== 'member') {
            throw ValidationException::withMessages([
                'circulation' => 'El socio no tiene una cuenta activa habilitada para operar.',
            ]);
        }
    }

    private function ensureItemCanBeRenewed(Item $item): void
    {
        if (! $item->is_active || ! $item->material?->is_active || $item->status?->name !== 'Prestado') {
            throw ValidationException::withMessages([
                'circulation' => 'No es posible renovar el préstamo de este ejemplar.',
            ]);
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
                'circulation' => 'El socio tiene sanciones activas y no puede renovar el préstamo.',
            ]);
        }
    }

    private function ensureNoPendingReservations(Item $item): void
    {
        $hasPendingReservations = Reservation::query()
            ->where('material_id', $item->material_id)
            ->whereHas('status', fn ($query) => $query->whereIn('name', Reservation::ACTIVE_STATUSES))
            ->exists();

        if ($hasPendingReservations) {
            throw ValidationException::withMessages([
                'circulation' => 'El material tiene reservas pendientes y no se puede renovar este préstamo.',
            ]);
        }
    }

    private function recordAudit(
        Circulation $loan,
        User $actor,
        \Illuminate\Support\Carbon $oldDueDate,
        \Illuminate\Support\Carbon $newDueDate,
        int $oldRenewalCount
    ): void {
        $audit = Audit::create([
            'affected_model' => Circulation::class,
            'action' => 'Renovar préstamo',
            'afected_record' => (string) $loan->id,
            'user_id' => $actor->id,
            'registration_date' => now(),
        ]);

        $audit->details()->createMany([
            [
                'field_name' => 'estimated_return_date',
                'old_value' => $oldDueDate->toDateTimeString(),
                'new_value' => $newDueDate->toDateTimeString(),
            ],
            [
                'field_name' => 'number_renewals',
                'old_value' => (string) $oldRenewalCount,
                'new_value' => (string) ($oldRenewalCount + 1),
            ],
        ]);
    }
}
