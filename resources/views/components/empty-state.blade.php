@props([
    'title' => 'Belum ada data',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-4']) }}>
    @if($icon)
        <div class="mb-4 text-zinc-400">{{ $icon }}</div>
    @endif

    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
        {{ $title }}
    </h3>

    @if($description)
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm">
            {{ $description }}
        </p>
    @endif

    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>