<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalRule extends Model
{
    public const SINGLETON_ID = 1;

    protected $table = 'global_rules';

    protected $fillable = [
        'max_simultaneous_loans',
        'max_simultaneous_reservations',
        'reservation_offer_hours',
        'max_loan_days',
        'max_renewals',
        'grace_hours',
        'max_consultation_hours',
        'max_sanction_days',
    ];

    protected function casts(): array
    {
        return [
            'max_simultaneous_loans' => 'integer',
            'max_simultaneous_reservations' => 'integer',
            'reservation_offer_hours' => 'integer',
            'max_loan_days' => 'integer',
            'max_renewals' => 'integer',
            'grace_hours' => 'integer',
            'max_consultation_hours' => 'integer',
            'max_sanction_days' => 'integer',
        ];
    }
}
