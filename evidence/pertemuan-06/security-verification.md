# Security Verification

- Token QR berupa opaque token acak; yang disimpan di database hanya hash-nya.
- Token mentah tampil satu kali setelah diterbitkan (flash session).
- Token tidak valid -> respons 404 generik, tanpa membocorkan alasan.
- Cookie sesi pelanggan: HttpOnly, SameSite=Lax.
- Rate limit qr-scan: 30 request/menit/IP -> 429.

Metode verifikasi: test otomatis (QrTableSessionTest). Uji manual cookie dan 429 tidak dilakukan.