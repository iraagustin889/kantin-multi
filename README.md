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