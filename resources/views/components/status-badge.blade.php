@props(['status' => 'default'])

@php
$map = [
    'pending'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'paid'      => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
    'cooking'   => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
    'ready'     => 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'default'   => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
];
$class = $map[$status] ?? $map['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium $class"]) }}>
    {{ $slot }}
</span>