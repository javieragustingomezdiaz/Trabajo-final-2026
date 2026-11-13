<x-layouts.app title="Iniciar sesión">
    <section class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200">
        <h1 class="text-2xl font-semibold text-slate-800">
            Iniciar sesión
        </h1>

        <p class="mt-2 text-sm text-slate-600">
            Ingresá con tu email o nombre de usuario.
        </p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="login" class="block text-sm font-medium text-slate-700">
                    Email o nombre de usuario
                </label>

                <input
                    id="login"
                    type="text"
                    name="login"
                    value="{{ old('login') }}"
                    autocomplete="username"
                    required
                    autofocus
                    class="mt-2 block h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-base text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('login')
                    <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">
                    Contraseña
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    required
                    class="mt-2 block h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-base text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('password')
                    <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember" class="flex items-center gap-2 text-sm text-slate-600">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    value="1"
                    @checked(old('remember'))
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                Recordarme
            </label>

            <button
                type="submit"
                class="h-12 w-full rounded-md bg-slate-700 px-4 font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
            >
                Ingresar
            </button>
            
        </form>
    </section>
</x-layouts.app>