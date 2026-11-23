<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Circulation;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\LoanStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirculationLostService
{
    public function markLost(Circulation $circulation, string $reason, User $actor): Circulation
    {
        return DB::transaction(function () use ($circulation, $reason, $actor): Circulation {
            $loan = Circulation::query()
                ->with('status')
                ->whereKey($circulation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->actual_return_date !== null || ! in_array($loan->status?->name, Circulation::ACTIVE_STATUSES, true)) {
                throw ValidationException::withMessages([
                    'circulation' => 'Solo se puede marcar como perdido un préstamo activo.',
                ]);
            }

            $member = User::query()->whereKey($loan->user_id)->lockForUpdate()->firstOrFail();
            $item = Item::query()
                ->with('status')
                ->whereKey($loan->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $item->is_active || $item->status?->name !== 'Prestado') {
                throw ValidationException::withMessages([
                    'circulation' => 'El ejemplar no figura como prestado; verificá su estado antes de marcarlo como perdido.',
                ]);
            }

            $oldLoanStatus = $loan->status?->name;
            $oldItemStatus = $item->status?->name ?? 'Sin estado';
            $wasMemberActive = (bool) $member->is_active;
            $lostLoanStatus = LoanStatus::firstOrCreate(['name' => Circulation::STATUS_LOST]);
            $lostItemStatus = ItemStatus::firstOrCreate(['name' => 'Perdido']);

            $loan->update([
                'loan_status_id' => $lostLoanStatus->id,
                'observation' => trim(($loan->observation ? $loan->observation . PHP_EOL : '')
                    . 'Préstamo marcado como perdido: ' . trim($reason)),
            ]);
            $item->update([
                'is_active' => false,
                'item_status_id' => $lostItemStatus->id,
            ]);
            $member->forceFill(['is_active' => false])->save();

            $this->recordAudit($loan, $item, $actor, $oldLoanStatus, $oldItemStatus, $wasMemberActive, trim($reason));

            return $loan->refresh()->load(['status', 'user.person', 'item.material']);
        });
    }

    private function recordAudit(
        Circulation $loan,
        Item $item,
        User $actor,
        ?string $oldLoanStatus,
        string $oldItemStatus,
        bool $wasMemberActive,
        string $reason
    ): void {
        $audit = Audit::create([
            'affected_model' => Circulation::class,
            'action' => 'Registrar préstamo perdido',
            'afected_record' => (string) $loan->id,
            'user_id' => $actor->id,
            'registration_date' => now(),
        ]);

        $audit->details()->createMany([
            ['field_name' => 'loan_status', 'old_value' => $oldLoanStatus, 'new_value' => Circulation::STATUS_LOST],
            ['field_name' => 'item_status', 'old_value' => $oldItemStatus, 'new_value' => 'Perdido'],
            ['field_name' => 'item_is_active', 'old_value' => 'Sí', 'new_value' => 'No'],
            ['field_name' => 'user_is_active', 'old_value' => $wasMemberActive ? 'Sí' : 'No', 'new_value' => 'No'],
            ['field_name' => 'reason', 'old_value' => null, 'new_value' => $reason],
        ]);
    }
}
