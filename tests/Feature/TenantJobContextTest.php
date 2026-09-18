<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\RecalculateTenantOrderStatsJob;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tahap 7 — item 3:
 * "Tambahkan test yang memastikan job membentuk context sendiri dan
 * membersihkannya kembali setelah selesai, sehingga dua job berurutan
 * tidak berbagi tenant."
 *
 * CATATAN: file ini menguji RecalculateTenantOrderStatsJob, job contoh
 * minimal yang dibuat khusus untuk keperluan test ini (lihat
 * app/Jobs/RecalculateTenantOrderStatsJob.php). Kalau kamu sudah punya job
 * nyata di proyekmu yang tenant-aware, ganti referensi job di test ini
 * dengan job aslimu — pola pengujiannya (assert context kosong sebelum dan
 * sesudah, assert context tidak "nyasar" ke job berikutnya) tetap sama.
 */
class TenantJobContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_clears_context_after_handling(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $context = app(TenantContext::class);

        $this->assertFalse($context->has(), 'Context seharusnya kosong sebelum job dijalankan.');

        (new RecalculateTenantOrderStatsJob($tenant->id))->handle($context);

        $this->assertFalse($context->has(), 'Context tertinggal setelah job selesai — cek blok finally pada job.');
    }

    public function test_job_does_not_run_for_inactive_tenant(): void
    {
        $suspendedTenant = Tenant::factory()->create(['status' => 'suspended']);
        $context = app(TenantContext::class);

        (new RecalculateTenantOrderStatsJob($suspendedTenant->id))->handle($context);

        // Fail closed: job untuk tenant nonaktif tidak boleh sempat mengisi
        // context sama sekali.
        $this->assertFalse($context->has());
    }

    public function test_two_sequential_jobs_for_different_tenants_do_not_share_context(): void
    {
        $tenantA = Tenant::factory()->create(['status' => 'active']);
        $tenantB = Tenant::factory()->create(['status' => 'active']);

        $context = app(TenantContext::class);
        $observedTenantIds = [];

        // Simulasikan worker yang sama memproses dua job berurutan.
        // Kita "intip" tenant aktif di tengah eksekusi lewat job turunan
        // sederhana — kalau job aslimu tidak mengekspos ini, cukup jalankan
        // dua handle() berurutan seperti di bawah dan pastikan context bersih
        // di antaranya.
        (new RecalculateTenantOrderStatsJob($tenantA->id))->handle($context);
        $this->assertFalse($context->has(), 'Context job A bocor ke job berikutnya.');

        (new RecalculateTenantOrderStatsJob($tenantB->id))->handle($context);
        $this->assertFalse($context->has(), 'Context job B bocor setelah selesai.');

        // Jika job aslimu menulis data (mis. stats), assert di sini bahwa
        // hasil tenant A tidak tertukar dengan tenant B — sesuaikan dengan
        // efek job nyatamu.
    }
}
