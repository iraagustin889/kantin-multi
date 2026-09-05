<x-layouts::app.tenant :title="__('Dashboard Tenant')">
    <div class="flex flex-col gap-4">
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">
            {{ __('Ringkasan Tenant') }}
        </h1>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Pesanan hari ini') }}</p>
                <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">0</p>
            </div>
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Pendapatan hari ini') }}</p>
                <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">Rp 0</p>
            </div>
        </div>

        <x-empty-state
            title="{{ __('Belum ada pesanan') }}"
            description="{{ __('Pesanan akan muncul di sini setelah Modul Ordering aktif.') }}"
        >
            <x-slot:action>
                <x-button variant="secondary">{{ __('Lihat menu') }}</x-button>
            </x-slot:action>
        </x-empty-state>
    </div>
</x-layouts::app.tenant>