@props([
    'label' => null,
    'name',
    'error' => null,
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">
            {{ $label }}
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full min-h-[44px] rounded-lg border px-3 py-2 text-sm
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                        border-zinc-300 dark:border-zinc-700
                        focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500
                        ' . ($error ? 'border-red-500 focus:ring-red-500' : '')
        ]) }}
    />

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>