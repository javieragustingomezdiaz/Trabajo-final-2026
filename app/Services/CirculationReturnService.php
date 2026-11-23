<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Circulation;
use App\Models\GlobalRule;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\ItemStatus;
use App\Models\LoanStatus;
use App\Models\LoanTerm;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirculationReturnService
{
    public function __construct(private readonly GlobalRuleService $globalRuleService)
    {
    }

    public function register(Circulation $circulation, bool $damaged, User $actor, ?string $returnNote = null): Circulation
    {
        return DB::transaction(function () use ($circulation, $damaged, $actor, $returnNote): Circulation {
            $loan = Circulation::query()
                ->with('status')
                ->whereKey($circulation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $loan->actual_return_date !== null
                || ! in_array($loan->status?->name, [Circulation::STATUS_ACTIVE, Circulation::STATUS_OVERDUE], true)
            ) {
                throw ValidationException::withMessages([
                    'circulation' => 'Este préstamo ya fue devuelto o no está activo.',
                ]);
            }

            $item = Item::query()
                ->with(['status', 'material.materialType.loanTerm'])
                ->whereKey($loan->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->status?->name !== 'Prestado') {
                throw ValidationException::withMessages([
                    'circulation' => 'El ejemplar no figura como prestado; verificá su estado antes de registrar la devolución.',
                ]);
            }

            $now = now();
            $isLate = $loan->estimated_return_date->isBefore($now);
            $oldStatus = $loan->status?->name;
            $oldItemStatus = $item->status?->name ?? 'Sin estado';
            $statusName = $damaged
                ? 'Finalizado con incidencias'
                : ($isLate ? 'Finalizado con retraso' : Circulation::STATUS_RETURNED);
            $newLoanStatus = LoanStatus::firstOrCreate(['name' => $statusName]);
            $newItemStatus = ItemStatus::firstOrCreate([
                'name' => $damaged ? 'En reparación' : 'Disponible',
            ]);

            $loan->update([
                'actual_return_date' => $now,
                'loan_status_id' => $newLoanStatus->id,
                'observation' => $this->returnObservation($loan->observation, $damaged, $isLate, $returnNote),
            ]);

            $itemChanges = ['item_status_id' => $newItemStatus->id];
            if ($damaged) {
                $damagedCondition = ItemCondition::query()->where('name', 'Dañado')->first();
                if ($damagedCondition) {
                    $itemChanges['item_condition_id'] = $damagedCondition->id;
                }
            }
            $item->update($itemChanges);

            $sanctionDays = $this->sanctionDays($loan, $item->material?->materialType?->loanTerm, $isLate, $damaged);
            if ($sanctionDays > 0) {
                $this->createSanction($loan, $now, $sanctionDays, $isLate, $damaged);
            }

            $this->recordAudit(
                $loan,
                $actor,
                $oldStatus,
                $statusName,
                $oldItemStatus,
                $newItemStatus->name,
                $damaged,
                $sanctionDays,
                $returnNote,
            );

            return $loan->refresh()->load(['status', 'user.person', 'item.material']);
        });
    }

    private function sanctionDays(Circulation $loan, ?LoanTerm $loanTerm, bool $isLate, bool $damaged): int
    {
        if (! $loanTerm) {
            return 0;
        }

        $days = 0;
        if ($isLate && $loanTerm->sanctions_days_factor) {
            $lateDays = max(1, (int) ceil($loan->estimated_return_date->diffInSeconds(now()) / 86400));
            $days += $lateDays * (int) $loanTerm->sanctions_days_factor;
        }
        if ($damaged) {
            $days += (int) ($loanTerm->damage_suspension_days ?? 0);
        }

        return min($days, (int) $this->globalRuleService->current()->max_sanction_days);
    }

    private function createSanction(Circulation $loan, Carbon $start, int $days, bool $isLate, bool $damaged): void
    {
        $reasons = [];
        if ($isLate) {
            $reasons[] = 'devolución fuera de término';
        }
        if ($damaged) {
            $reasons[] = 'devolución con daños';
        }

        Sanction::create([
            'start_on' => $start,
            'end_on' => $start->copy()->addDays($days),
            'reason' => ucfirst(implode(' y ', $reasons)),
            'status' => 'Activa',
            'observation' => "Sanción de {$days} día(s) generada al registrar la devolución.",
            'circulation_id' => $loan->id,
            'user_id' => $loan->user_id,
        ]);
    }

    private function returnObservation(?string $observation, bool $damaged, bool $isLate, ?string $returnNote): ?string
    {
        $notes = [];
        if ($damaged) {
            $notes[] = 'Devuelto con daños.';
        }
        if ($isLate) {
            $notes[] = 'Devuelto fuera de término.';
        }
        if ($returnNote !== null && trim($returnNote) !== '') {
            $notes[] = 'Observación de devolución: ' . trim($returnNote);
        }

        if ($notes === []) {
            return $observation;
        }

        return trim(($observation ? $observation . PHP_EOL : '') . implode(' ', $notes));
    }

    private function recordAudit(
        Circulation $loan,
        User $actor,
        ?string $oldStatus,
        string $newStatus,
        string $oldItemStatus,
        string $newItemStatus,
        bool $damaged,
        int $sanctionDays,
        ?string $returnNote
    ): void {
        $audit = Audit::create([
            'affected_model' => Circulation::class,
            'action' => 'Registrar devolución',
            'afected_record' => (string) $loan->id,
            'user_id' => $actor->id,
            'registration_date' => now(),
        ]);

        $audit->details()->createMany([
            ['field_name' => 'actual_return_date', 'old_value' => null, 'new_value' => $loan->actual_return_date?->toDateTimeString()],
            ['field_name' => 'loan_status', 'old_value' => $oldStatus, 'new_value' => $newStatus],
            ['field_name' => 'item_status', 'old_value' => $oldItemStatus, 'new_value' => $newItemStatus],
            ['field_name' => 'returned_damaged', 'old_value' => null, 'new_value' => $damaged ? 'Sí' : 'No'],
            ['field_name' => 'sanction_days', 'old_value' => null, 'new_value' => (string) $sanctionDays],
            ['field_name' => 'return_note', 'old_value' => null, 'new_value' => $returnNote],
        ]);
    }
}
