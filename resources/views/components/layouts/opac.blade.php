<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Catálogo público | Archivium' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-slate-800">
    <header class="bg-slate-800 text-white shadow">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('opac.index') }}" class="text-xl font-semibold">Archivium · Catálogo</a>
            <nav class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:underline">Panel</a>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Iniciar sesión</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="mt-12 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-5 text-sm text-slate-500 sm:px-6 lg:px-8">
            Catálogo público de la biblioteca
        </div>
    </footer>
</body>
</html>
