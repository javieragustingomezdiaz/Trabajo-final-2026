<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            ['name'] => ['required', 'string'],
            ['email'] => ['required', 'email', 'max:255', 'unique:users,email'],
            ['password'] => ['required', 'confirm', Password::min(8)],
        ]);

        Auth::login(User::create($data));
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
