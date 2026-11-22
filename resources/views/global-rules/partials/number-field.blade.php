<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $field[0] }}</label>
    <div class="flex items-center gap-2">
        <input id="{{ $name }}" name="{{ $name }}" type="number" min="0" step="1" required value="{{ old($name, $globalRule->{$name}) }}"
               class="w-28 rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
        <span class="text-sm text-slate-500">{{ $field[1] }}</span>
    </div>
    @if (! empty($field[2]))<p class="mt-1 text-xs text-slate-500">{{ $field[2] }}</p>@endif
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
