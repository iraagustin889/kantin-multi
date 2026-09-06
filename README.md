# Kantin Multi-Tenant

Aplikasi kantin multi-tenant dibangun dengan Laravel 13 + Livewire 4.

## Requirements
- PHP >= 8.4
- Composer
- Node.js & NPM
- MySQL/MariaDB [via servbay]
- Redis

## Setup
1. Clone repository ini
2. Copy `.env.example` menjadi `.env`
3. Jalankan `composer install`
4. Jalankan `php artisan key:generate`
5. Isi variabel `DB_*`, `REDIS_*`, dan `REVERB_*` di `.env` sesuai environment lokal
6. Jalankan `npm install`

## Run
```bash
php artisan migrate:fresh --seed
composer run dev        # menjalankan server, queue worker, dan Vite
php artisan reverb:start  # di terminal terpisah
```
Aplikasi bisa diakses di `http://localhost:8000`.

## Test
```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

## Troubleshooting
| Gejala | Penyebab | Perbaikan |
|---|---|---|
| `could not find driver` | Ekstensi `pdo_mysql` belum aktif | Aktifkan ekstensi, restart PHP |
| Port bentrok (3306/6379) | Service lain masih jalan | Ganti port di `.env` atau matikan service lain |
| Halaman tanpa CSS/JS | Aset belum dibangun | Jalankan `npm install && npm run build` |




AFTER PRAKTIKUM PERTEMUAN 3
## Reset Database (Development)

> ⚠️ **PERINGATAN**: Perintah di bawah ini menghapus SELURUH tabel pada
> koneksi database yang aktif. Hanya jalankan pada database
> development/testing yang memang boleh dikosongkan. Jangan pernah
> jalankan pada database yang berisi data yang masih diperlukan.

Untuk membangun ulang schema dari nol beserta data demo:

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
1. Menghapus seluruh tabel pada koneksi database aktif
2. Menjalankan seluruh migrasi dari awal
3. Menjalankan `DatabaseSeeder` (termasuk `DemoCanteenSeeder`) untuk
   mengisi data demo: 1 kantin, 2 tenant, menu, modifier, dan komisi

### Verifikasi cepat setelah reset

```bash
php artisan migrate:status      # pastikan semua migrasi "Ran"
php artisan test --filter=TenantIsolationConstraintTest   # pastikan constraint DB masih ditegakkan
```

### Membekukan schema sebagai dokumentasi

```bash
php artisan schema:dump
```

Hasilnya tersimpan di `database/schema/mysql-schema.sql`.