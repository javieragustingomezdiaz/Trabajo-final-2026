<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function membershipApplicants(): HasMany
    {
        return $this->hasMany(MembershipApplication::class);
    }
}
