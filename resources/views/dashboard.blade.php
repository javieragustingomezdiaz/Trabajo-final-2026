<x-layouts.app title="Panel">
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</x-layouts.app>