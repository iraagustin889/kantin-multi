<x-layouts::app.customer :title="__('Menu Kantin')">
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">
                {{ __('Selamat datang') }}
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Pilih tenant untuk mulai memesan.') }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <x-status-badge status="pending">{{ __('Menunggu') }}</x-status-badge>
            <x-status-badge status="cooking">{{ __('Diproses') }}</x-status-badge>
            <x-status-badge status="ready">{{ __('Siap diambil') }}</x-status-badge>
        </div>

        <x-empty-state
            title="{{ __('Belum ada tenant') }}"
            description="{{ __('Data tenant akan tampil di sini setelah Modul 3 (Catalog) selesai.') }}"
        >
            <x-slot:action>
                <x-button variant="primary">{{ __('Muat ulang') }}</x-button>
            </x-slot:action>
        </x-empty-state>
    </div>
</x-layouts::app.customer>