<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanStatus extends Model
{
    protected $fillable = ['name'];

    public function circulations(): HasMany
    {
        return $this->hasMany(Circulation::class, 'loan_status_id');
    }
}
