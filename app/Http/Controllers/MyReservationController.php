<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\View\View;

class MyReservationController extends Controller
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

        $estadosActivos = [
            Reservation::STATUS_WAITING,
            Reservation::STATUS_NOTIFIED,
            Reservation::STATUS_READY,
        ];

        $reservations = Reservation::query()
            ->with(['material.materialType', 'item', 'status'])
            ->where('user_id', $user->id)
            ->whereHas('status', function ($query) use ($vista, $estadosActivos): void {
                if ($vista === 'activas') {
                    $query->whereIn('name', $estadosActivos);
                } else {
                    $query->whereNotIn('name', $estadosActivos);
                }
            })
            ->orderByDesc('registration_date')
            ->paginate(10)
            ->withQueryString();

        return view('reservations.mine', compact('reservations', 'vista'));
    }
}
