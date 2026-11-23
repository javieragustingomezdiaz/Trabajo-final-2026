<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->is_active) {
            return $next($request);
        }

        $reason = $user->accountDeactivationReason();
        $message = $reason
            ? 'Tu cuenta está deshabilitada. Motivo: ' . $reason
            : 'Tu cuenta está deshabilitada. Contactá al personal de la biblioteca.';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('account_disabled_message', $message);
    }
}
