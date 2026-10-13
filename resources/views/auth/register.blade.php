<x-layouts.app title="Registro">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label>Nombre</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Correo</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Contraseña</label>
            <input type="password" name="password">
            @error('password') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirmation">
        </div>

        <button type="submit">Registrarme</button>
        <a href="{{ route('login') }}">¿Ya tienes cuenta?</a>
    </form>
</x-layouts.app>
