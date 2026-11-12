<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $login = trim($data['login']);
        $key = Str::transliterate(Str::lower($login).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'login' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }

        $user = User::query()
            ->where('is_active', true)
            ->whereHas('person', function ($personQuery) use ($login): void {
                $personQuery->where('email', $login)
                    ->orWhere('name', $login)
                    ->orWhereRaw("CONCAT(name, ' ', last_name) = ?", [$login]);
            })
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($key);

            throw ValidationException::withMessages([
                'login' => 'Las credenciales no son correctas.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
