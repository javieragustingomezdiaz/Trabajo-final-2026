<form method="POST" action="{{ route('materials.store') }}">
    @csrf

    <input type="hidden" name="material_type_id" value="{{ $materialType->id }}">

    <label>
        Título
        <input name="title" value="{{ old('title') }}" required>
    </label>

    <livewire:catalog.material-contributors />

    <button type="submit">Guardar material</button>
</form>