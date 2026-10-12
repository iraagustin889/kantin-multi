# Quality Gate - Pertemuan 06

| Pemeriksaan | Perintah | Hasil |
|---|---|---|
| Daftar route | php artisan route:list --path=q | customer.scan terdaftar |
| Test | php artisan test --compact tests\Feature\QrTableSessionTest.php | [passed / failed] |
| Format kode | vendor\bin\pint --test | [lulus / belum dijalankan] |
