# Pertemuan 04 — Security Verification (matriks allow/deny)

Isolasi berlapis tenant A vs B, diuji di MariaDB nyata.

| # | Skenario | Ekspektasi | Hasil |
|---|---|---|---|
| 1 | Global scope: query context=A | hanya baris tenant A | ✅ (2 vs 5) |
| 2 | Tanpa context | scope tidak memfilter (mitigasi: route selalu isi context) | ✅ |
| 3 | Context scoped reset antar request/job | tidak bocor | ✅ forgetScopedInstances → has()=false |
| 4 | Auto-fill tenant_id saat create dalam context | tenant_id = A | ✅ |
| 5 | `GET tenant/{A}/menus` sebagai anggota | hanya menu A | ✅ (2 item) |
| 6 | `GET tenant/{A}/menus/{menuB}` | scoped binding → 404 | ✅ NotFound |
| 7 | `PATCH tenant/{A}/menus/{menuA}` anggota | 200 toggle | ✅ |
| 8 | `MenuPolicy::update` menu tenant lain | false | ✅ ditolak |
| 9 | Public catalog canteen1 | hanya tenant aktif canteen1 (bukan canteen2/suspended) | ✅ (2 item) |
| 10 | Job per tenant (A lalu B) | context init+clear, tak terkontaminasi | ✅ (2, lalu 3) |
| 11 | Tenant suspended / non-anggota → dashboard | 403 | ✅ |
| 12 | Guest → route tenant/admin | redirect login | ✅ |

Composite FK DB (Modul 3) tetap menjadi benteng penyimpanan (cross-tenant insert → 1452).


## Tahap 7 — Security Tests

### Matriks isolasi tenant (tests/Feature/TenantIsolationTest.php)
- Menu: index, show, update, swap-URL → ✅ lolos (403/404 sesuai ekspektasi, data tidak berubah)
- Menu: delete → ⏭️ belum bisa diuji, route `tenant.menus.destroy` belum dibangun
- Order (index/show/update/delete) → ⏭️ belum bisa diuji, route `tenant.orders.*` belum dibangun
- Withdrawal (index/show/update/delete) → ⏭️ belum bisa diuji, route `tenant.withdrawals.*` belum dibangun

Test sudah ditulis lengkap untuk ketiga resource dan otomatis akan jalan
begitu route-nya dibuat (tidak perlu ubah test). Assertion 403/404 + data
tidak berubah, sesuai kriteria modul.

### Katalog publik (tests/Feature/PublicCatalogQueryTest.php)
✅ 3/3 lolos — katalog hanya menampilkan menu aktif dari canteen yang diminta,
tidak bocor antar canteen, tidak bocor tenant nonaktif.

### Job context lifecycle (tests/Feature/TenantJobContextTest.php)
✅ 3/3 lolos — job membentuk context sendiri dan membersihkannya di finally,
dua job berurutan untuk tenant berbeda tidak berbagi context.
Diuji dengan job contoh RecalculateTenantOrderStatsJob karena belum ada
job tenant-aware nyata di project.

### Composite FK constraint (tests/Feature/TenantIsolationConstraintTest.php)
✅ 5/5 lolos — FK komposit menolak referensi lintas tenant di level database
(modifier_options, menus, order_items), unique constraint tenant code dan
idempotency key bekerja.
