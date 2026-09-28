# Known Issues - Pertemuan 5

## Formula komisi
- komisi = subtotal x rate snapshot
- Versi komisi berlaku pada interval setengah-terbuka [valid_from, valid_to)

## Perbedaan lingkungan
- Memakai MySQL (bukan MariaDB) karena MariaDB gagal terpasang di ServBay.
- Kolom `before`/`after` di `audit_logs` bertipe `json` (modul: `longtext`).
  Tidak memengaruhi perilaku karena cast `array` di model AuditLog tetap bekerja.
- Menjalankan di Windows (cmd), sedangkan contoh output modul diambil dari Linux/Mac.

## Masalah yang ditemui dan solusinya
- Teks kode di PDF terpotong saat disalin (mis. `use Database` / `Factories\...`
  dan `use ...\Model;` hilang di CommissionScheme.php). Diperbaiki manual;
  `php -l` dan test lulus.
- `AdminTenantController::edit()` error `Undefined variable $bankAccounts`
  karena baris pembuatannya belum ada. Ditambahkan sesuai Langkah 5.3.
- `ModuleConventionTest` gagal di Windows karena beda pemisah path (`\` vs `/`).
  Diperbaiki dengan menormalisasi path sebelum dibandingkan.
- `vendor\bin\pint --test` melaporkan 23 file dengan masalah format.
  Diselesaikan dengan menjalankan `vendor\bin\pint`.
- Perintah test di PDF terpotong jadi dua baris; sebenarnya satu perintah.

## Ketidaksesuaian pada modul (temuan)
- Status rekening di kode `unverified`, sedangkan teks/evidence menulis `pending`.
- Route verify rekening dan makePrimary sudah ada, tetapi belum ada tombolnya di view.
  Form role juga berupa teks bebas dan belum ada tombol cabut role.
- `TenantPolicy::viewAny` mengizinkan role `viewer`, tetapi controller memakai
  `canteen()` yang hanya menerima owner/manager/finance, sehingga viewer tetap 403.
- Cuplikan kode overlap komisi di halaman "Cuplikan Kode" tidak sama dengan
  implementasi `ChangeCommissionSchedule` yang sebenarnya.
- Jumlah test di modul (12 / 74 / 93 passed) tidak sinkron antar bagian.

## Hasil akhir
- php artisan test: (isi hasil asli, mis. XX passed)
- vendor\bin\pint --test: PASS
- npm run build: sukses (vite build, built in 5.85s)