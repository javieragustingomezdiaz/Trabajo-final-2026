<x-layouts.app title="Iniciar sesión">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="login">Email o nombre de usuario</label>
            <input
                id="login"
                type="text"
                name="login"
                value="{{ old('login') }}"
                autocomplete="username"
                required
                autofocus
            >
            @error('login')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >
            @error('password')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <label for="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            Recordarme
        </label>

        <button type="submit">Iniciar sesión</button>
        
    </form>
</x-layouts.app>