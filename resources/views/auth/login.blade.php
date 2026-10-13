<x-layouts.app title="Iniciar sesión">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label>Correo</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Contraseña</label>
            <input type="password" name="password">
        </div>

        <label>
            <input type="checkbox" name="remember"> Recordarme
        </label>

        <button type="submit">Iniciar sesión</button>
        <a href="{{ route('register') }}">Crear cuenta</a>
    </form>
</x-layouts.app>