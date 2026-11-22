<?php

namespace App\Services;

use App\Models\GlobalRule;

class GlobalRuleService
{
    public function current(): GlobalRule
    {
        return GlobalRule::query()->firstOrNew(
            ['id' => GlobalRule::SINGLETON_ID],
            $this->defaults(),
        );
    }

    public function update(array $attributes): GlobalRule
    {
        return GlobalRule::query()->updateOrCreate(
            ['id' => GlobalRule::SINGLETON_ID],
            $attributes,
        );
    }

    private function defaults(): array
    {
        return [
            'max_simultaneous_loans' => 3,
            'max_simultaneous_reservations' => 5,
            'reservation_offer_hours' => 6,
            'max_loan_days' => 14,
            'max_renewals' => 2,
            'grace_hours' => 1,
            'max_consultation_hours' => 4,
            'max_sanction_days' => 30,
        ];
    }
}
