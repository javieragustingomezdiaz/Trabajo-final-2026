<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Archivium' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100">
    @auth
        <div class="min-h-screen md:flex">
            <aside class="flex min-h-screen w-16 shrink-0 flex-col bg-slate-800 text-slate-100 md:sticky md:top-0 md:h-screen md:w-60">
                <div class="border-b border-slate-600 px-3 py-5 md:px-6">
                    <a href="{{ route('dashboard') }}" class="font-semibold">
                        <span class="md:hidden" aria-hidden="true">A</span>
                        <span class="hidden md:inline">Archivium</span>
                    </a>
                </div>

                <nav class="flex flex-1 flex-col gap-2 p-2 md:p-4" aria-label="Navegación principal">
                    <a href="{{ route('dashboard') }}"
                       title="Inicio"
                       class="rounded px-3 py-2 hover:bg-slate-700">
                        <span class="md:hidden" aria-hidden="true">⌂</span>
                        <span class="hidden md:inline">Inicio</span>
                    </a>

                    @if (auth()->user()->role?->name === 'admin')
                        <a href="{{ route('roles.index') }}"
                           title="Roles"
                           class="rounded px-3 py-2 hover:bg-slate-700">
                            <span class="md:hidden" aria-hidden="true">⚙</span>
                            <span class="hidden md:inline">Roles</span>
                        </a>
                    @endif

                    @if (in_array(auth()->user()->role?->name, ['admin', 'librarian'], true))
                        <a href="{{ route('materials.create') }}"
                        title="Registrar material"
                        class="rounded px-3 py-2 hover:bg-slate-700">
                            <span class="md:hidden" aria-hidden="true">＋</span>
                            <span class="hidden md:inline">Registrar material</span>
                        </a>
                    @endif
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="p-2 md:p-4">
                    @csrf
                    <button type="submit"
                            title="Cerrar sesión"
                            class="w-full rounded px-3 py-2 text-left hover:bg-slate-800">
                        <span class="md:hidden" aria-hidden="true">↪</span>
                        <span class="hidden md:inline">Cerrar sesión</span>
                    </button>
                </form>
            </aside>

            <main class="min-w-0 flex-1 p-4 md:p-8">
                {{ $slot }}
            </main>
        </div>
    @else
        <main class="grid min-h-screen place-items-center p-4">
            {{ $slot }}
        </main>
    @endauth
</body>
</html>