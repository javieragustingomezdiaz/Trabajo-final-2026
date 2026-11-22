<?php

namespace App\Policies;

use App\Models\LoanTerm;
use App\Models\User;

class LoanTermPolicy
{
    public function manage(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role?->name, ['admin', 'librarian'], true);
    }
}
