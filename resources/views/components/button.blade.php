@props([
    'variant' => 'primary', // primary | secondary | danger
    'type' => 'button',
])

@php
$base = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 min-h-[44px] text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary'   => 'bg-orange-600 text-white hover:bg-orange-700 focus:ring-orange-500',
    'secondary' => 'bg-white text-zinc-700 border border-zinc-300 hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 dark:hover:bg-zinc-700 focus:ring-zinc-400',
    'danger'    => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</button>