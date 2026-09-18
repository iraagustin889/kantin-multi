<?php

namespace App\Support\Tenancy;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Mencatat setiap akses lintas tenant yang dilakukan admin platform/canteen
 * (misalnya lewat PublicCatalogQuery atau laporan admin lainnya yang sengaja
 * melewati global scope tenant). Checklist Tahap 6, Modul 4.
 *
 * Catatan status: kelas ini sudah siap dipakai, tetapi saat ini BELUM ada
 * fitur admin lintas-tenant yang memanggilnya (belum ada controller/laporan
 * admin platform di project ini). Panggil log() ini begitu fitur tersebut
 * dibangun, di titik yang sama tempat withoutGlobalScope()/whereHas() dipakai.
 */
final class CrossTenantAccessLogger
{
    /**
     * @param  User    $actor   User yang melakukan akses lintas tenant.
     * @param  string  $scope   Lingkup data yang diakses, mis. "canteen:1" atau "all-tenants".
     * @param  string  $reason  Alasan/fitur pemicu, mis. "public-catalog", "admin-report".
     */
    public static function log(User $actor, string $scope, string $reason): void
    {
        Log::channel('single')->info('cross_tenant_access', [
            'actor_id' => $actor->id,
            'actor_email' => $actor->email,
            'actor_role' => $actor->role,
            'scope' => $scope,
            'reason' => $reason,
            'occurred_at' => now()->toIso8601String(),
        ]);
    }
}
