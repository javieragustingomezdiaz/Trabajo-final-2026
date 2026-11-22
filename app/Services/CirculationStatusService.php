<?php

namespace App\Services;

use App\Models\Circulation;
use App\Models\LoanStatus;

class CirculationStatusService
{
    public function markOverdue(): int
    {
        $overdueStatus = LoanStatus::firstOrCreate([
            'name' => Circulation::STATUS_OVERDUE,
        ]);

        return Circulation::query()
            ->whereNull('actual_return_date')
            ->where('estimated_return_date', '<', now())
            ->whereHas('status', fn ($query) => $query->where('name', Circulation::STATUS_ACTIVE))
            ->update(['loan_status_id' => $overdueStatus->id]);
    }
}
