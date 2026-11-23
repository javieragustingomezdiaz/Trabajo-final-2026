<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyLoanController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'vista' => ['nullable', 'in:activas,historial'],
        ]);

        $user = $request->user();

        abort_unless(
            $user->is_active
                && $user->role?->is_active
                && $user->role?->name === 'member',
            403
        );

        $vista = $request->query('vista', 'activas');


        $circulations = Circulation::query()
            ->with(['item.material.materialType', 'status'])
            ->where('user_id', $user->id)
            ->whereHas('status', function ($query) use ($vista): void {
                if ($vista === 'activas') {
                    $query->whereIn('name', Circulation::ACTIVE_STATUSES);
                } else {
                    $query->whereNotIn('name', Circulation::ACTIVE_STATUSES);
                }
            })
            ->orderByDesc('loan_date')
            ->paginate(10)
            ->withQueryString();

        return view('circulations.mine', compact('circulations','vista'));
    }
    

}
