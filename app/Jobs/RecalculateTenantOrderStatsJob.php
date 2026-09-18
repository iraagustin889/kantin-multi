<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Contoh job minimal untuk keperluan Tahap 7 (uji isolasi context pada queue worker).
 *
 * PENTING: kalau kamu sudah punya job nyata di proyekmu (mis. job export order,
 * job notifikasi, dsb), ganti referensi job ini di
 * tests/Feature/TenantJobContextTest.php dengan job aslimu — jangan pakai job
 * contoh ini di production, ini murni skeleton untuk membuktikan pola
 * "job membawa tenant_id eksplisit, bentuk context sendiri, bersihkan di finally".
 */
class RecalculateTenantOrderStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Payload job HANYA menyimpan tenant_id (bukan object Tenant penuh),
     * karena data/scope tenant bisa berubah sebelum job diproses (lihat Tahap 2).
     */
    public function __construct(
        public readonly int $tenantId,
    ) {}

    public function handle(TenantContext $context): void
    {
        try {
            $tenant = Tenant::query()->find($this->tenantId);

            // Fail closed: tenant sudah dihapus atau nonaktif → job berhenti,
            // bukan diam-diam jalan tanpa context yang valid.
            if ($tenant === null || $tenant->status !== 'active') {
                return;
            }

            $context->set($tenant);

            // ... logika domain job yang sesungguhnya, membaca tenant aktif dari $context ...
        } finally {
            // Wajib: worker yang sama bisa langsung memproses job tenant lain
            // setelah ini, context tidak boleh tertinggal.
            $context->clear();
        }
    }
}
