<?php

namespace App\Policies;

use App\Models\GlobalRule;
use App\Models\User;

class GlobalRulePolicy
{
    public function manage(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role?->name, ['admin', 'librarian'], true);
    }
}
