<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MembershipApplication extends Model
{
    public function person(): BelongsTo 
    {
        return $this->belongsTo(Person::class);
    }

    public function userApproval(): HasOne
    {
        return $this->hasOne(UserApproval::class);
    }
}
