<div align="center">

# 📊 E-2: Cash Flow & Executive Dashboard
### Agregator Keuangan Terpadu & Dasbor Eksekutif Perusahaan (Stateless Read-Only)
**ERP PT Anaheim Nimbus Universal** · Capstone Project PPL Kelas E

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-11.x/12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white">
  <img alt="Status" src="https://img.shields.io/badge/Status-Demo%20Ready-2EA44F?style=flat-square">
</p>

</div>

---

## 💡 Tentang Modul E-2

Modul **E-2** bertindak sebagai *nerve center* finansial bagi PT Anaheim Nimbus Universal. Modul ini beroperasi secara **stateless dan read-only**, berfungsi murni sebagai **agregator terdistribusi** yang menghimpun, mengolah, dan menyajikan metrik arus kas, profitabilitas proyek, serta indikator kesehatan keuangan secara dinamis dari berbagai sistem hulu ERP untuk kebutuhan direksi.

---

### 🌐 Ekosistem Integrasi Lintas Modul
E-2 terhubung secara terdistribusi dengan berbagai modul hulu melalui HTTP Client dan token otentikasi:

- **Modul F-1 (Autentikasi & Manajemen Pengguna):** Validasi *Bearer Token* JWT, sinkronisasi *secret/claims* (`accountId`, `roleCode`), dan manajemen sesi lintas layanan.
- **Modul D-1 (Registrasi Proyek):** Sinkronisasi master data proyek, metadata, dan status operasional.
- **Modul D-2 (Kontrak & Invoice):** Penarikan data termin pembayaran, nilai kontrak, dan status penagihan.
- **Modul E-1 (Pajak & e-Faktur):** Konsumsi ringkasan kewajiban pajak keluaran secara dinamis berdasarkan parameter `taxPeriod` (format `YYYY-MM`).
- **Modul F-5 (Payroll & Gaji):** Resolusi UUID periode secara dinamis berdasarkan bulan yang dipilih untuk menarik rekapitulasi pengeluaran gaji dan BPJS.

---

## ⚡ Fitur & Halaman Utama

- **Dasbor Interaktif & Filter Bulan (`/dashboard` & `/dashboard/pengeluaran`)**: Antarmuka web berbasis Blade dengan fitur filter periode bulanan (Oktober, November, Desember 2026, dll.) yang terhubung secara *live* ke E-1 dan F-5.
- **Executive Financial Summary (`/api/v1/dashboard/aggregated-summary`)**: Agregasi metrik kesehatan perusahaan global (Total Proyeksi Pendapatan, Beban Pajak, Beban Payroll, dan *Net Cash Flow*).
- **Monthly Trend Analysis (`/api/v1/cashflow/monthly`)**: Visualisasi tren arus kas bulanan berdasarkan parameter tahun operasional.
- **Project Profitability Drill-Down (`/api/v1/cashflow/project/{projectId}`)**: Analisis keuangan mendalam per proyek.
- **Financial Health Indicators (`/api/v1/dashboard/financial-indicators`)**: Evaluasi indikator kesehatan finansial otomatis.

---

## 🛡️ Resiliensi & Penanganan Error (*Graceful Fallback*)
Untuk menjaga kestabilan demo, E-2 dilengkapi sistem penanganan error tingkat lanjut:
* **Timeouts & Upstream Protection**: Pembatasan waktu tunggu (*timeout*) koneksi ke modul hulu (2-5 detik) guna mencegah kemacetan (*bottleneck*).
* **Safe Fallback**: Apabila modul hulu mengalami *offline*, *timeout*, atau data belum tersedia, sistem secara otomatis mengembalikan nilai *default* (nol/kosong) alih-alih merusak tampilan (*crash*), lengkap dengan opsi penanganan sesi kedaluwarsa (`401 Unauthorized`).

---

## 🚀 Panduan Instalasi Cepat

1. **Clone dan pasang dependensi**
```bash
composer install
```
2. **Konfigurasi Environment**
```bash
   cp .env.example .env
   php artisan key:generate
```
3. **Atur URL Layanan Hulu di .env**
```bash
   F1_SERVICE_URL=http://127.0.0.1:4100
   D1_SERVICE_URL=http://127.0.0.1:3000/api/v1
   D2_SERVICE_URL=http://127.0.0.1:8000
   E1_SERVICE_URL=http://127.0.0.1:8087
   F5_SERVICE_URL=http://127.0.0.1:8001
```
4. **Jalankan Server Lokal**
```bash
   php artisan serve
```

---

## 📡 Daftar Endpoint API Utama
Endpoint API di bawah ini (/api/v1/...) disediakan sebagai contract layer / layanan headless untuk keperluan pengujian otomatis (Postman/Swagger), integrasi dengan sistem luar, atau konsumsi klien alternatif. Adapun antarmuka web utama (Blade) E-2 merender data secara langsung melalui pemanggilan Service internal (ErpIntegrationService), bukan dengan memanggil endpoint API ini secara asinkron.
| Endpoint | Metode | Deskripsi | Status Code |
|---|---|---|---|
|/api/dashboard/aggregated-summary|GET|Dasbor agregat dari modul D-1, D-2, E-1, F-5| 200, 401, 403, 503 |
|/api/v1/cashflow/monthly|GET|Tren arus kas bulanan|200, 400, 401, 403, 503|
|/cashflow/project/{projectId}|GET|Detail profitabilitas dan arus kas proyek tertentu|200, 400, 404, 401, 403, 503|
|/dashboard/financial-indicators|GET|Evaluasi indikator kesehatan finansial perusahaan|200, 401, 403, 503|

---

## 🔒 Keamanan & Penanganan Kesalahan

Sistem menerapkan standar respons JSON seragam dengan kode status HTTP yang jelas:

* 200 OK (E2-200): Permintaan berhasil diproses.

* 400 Bad Request (ERR-REQ-400): Parameter query yang dikirimkan tidak valid (misalnya format taxPeriod salah atau projectId salah).

* 401 Unauthorized (ERR-AUTH-401): Token akses tidak valid, kedaluwarsa, atau tidak disertakan pada header Authorization.

* 403 FORBIDDEN (ERR-AUTH-403): Akun pengguna tidak memiliki hak akses tingkat eksekutif.

* 404 Not Found (ERR-NOT-FOUND-404): Data atau ID Proyek (projectId) spesifik tidak ditemukan pada sistem sumber hulu.

* 503 UPSTREAM_SERVICE_UNAVAILABLE (ERR-DS-503): Modul hulu (D-2/E-1/F-5) mengalami kendala koneksi, timeout (lebih dari 2 detik), atau data belum final.

---

## 👥 Tim Pengembang

Kelas : E
Kelompok : 2
Modul : E-2 | Arus Kas & Dasbor Keuangan

| Anggota | NIM |
|---|---|
| Ariska Putri | 233040041 |
| Haifa Zahirah Ramdhan | 233040051 |
| Daffa Kamiliya Mufidah | 233040055 |
| Ellen Aplida Zalni | 233040061 |
| Emeralda Iffatud Diana | 233040065 |


