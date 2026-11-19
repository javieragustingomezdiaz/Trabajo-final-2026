<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTerm extends Model
{
    protected $fillable = [
        'bookable',
        'hours_on_standby',
        'is_home_loanable',
        'home_loan_days',
        'allow_in_library_use',
        'in_library_loan_hours',
        'is_renewable',
        'max_renewal',
        'renewal_days',
        'max_simultaneous_reservations',
        'max_simultaneous_loans',
        'sanctions_days_factor',
        'damage_suspension_days',
        'material_type_id',
    ];

    protected function casts(): array
    {
        return [
            'bookable' => 'boolean',
            'hours_on_standby' => 'integer',
            'max_simultaneous_reservations' => 'integer',
            'max_simultaneous_loans' => 'integer',
        ];
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }
}
