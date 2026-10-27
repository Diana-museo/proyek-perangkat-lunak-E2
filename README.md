<div align="center">

# 📊 E-2: Cash Flow & Executive Dashboard
### Agregator Keuangan Terpadu & Dasbor Eksekutif Perusahaan (Stateless Read-Only)
**ERP PT Anaheim Nimbus Universal** · Capstone Project PPL Kelas E

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13.0-FF2D20?style=flat-square&logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white">
  <img alt="PostgreSQL" src="https://img.shields.io/badge/PostgreSQL-16%2B-4169E1?style=flat-square&logo=postgresql&logoColor=white">
  <img alt="Status" src="https://img.shields.io/badge/Status-Production%20Ready-2EA44F?style=flat-square">
</p>

</div>

---

## 💡 Tentang Modul E-2

Modul **E-2** bertindak sebagai *nerve center* finansial bagi PT Anaheim Nimbus Universal. Modul ini beroperasi secara **stateless dan read-only**, artinya modul ini tidak menyimpan database transaksi mandiri atau menerima input manual, melainkan berfungsi murni sebagai **agregator terdistribusi** yang menghimpun, mengolah, dan menyajikan metrik arus kas, profitabilitas proyek, serta indikator kesehatan keuangan secara *real-time* dari berbagai sistem hulu ERP untuk kebutuhan direksi.

---

### 🌐 Ekosistem Integrasi Lintas Modul
E-2 terhubung secara terdistribusi dengan berbagai modul hulu dalam arsitektur ERP:

- **Modul F-1 (Autentikasi & Manajemen Pengguna):** Validasi *Bearer Token*, manajemen sesi, dan kontrol hak akses berbasis peran (RBAC) untuk Direksi dan Kepala Finance.
- **Modul D-1 (Registrasi Proyek):** Sinkronisasi master data proyek, alokasi anggaran, dan status operasional.
- **Modul D-2 (Kontrak & Invoice):** Penarikan data tagihan masuk, termin, dan piutang klien.
- **Modul E-1 (Pajak & e-Faktur):** Konsumsi ringkasan kewajiban pajak keluaran melalui endpoint agregasi.
- **Modul F-5 (Payroll & Gaji):** Rekapitulasi pengeluaran operasional rutin untuk pembayaran gaji pegawai.

---

## ⚡ Fitur Unggulan

- **Executive Financial Summary (`/dashboard/aggregated-summary`)**: Agregasi metrik kesehatan perusahaan secara global (Total Proyeksi Pendapatan, Beban Pajak, Beban Payroll, dan *Net Cash Flow*).
- **Monthly Trend Analysis (`/cashflow/monthly`)**: Visualisasi tren arus kas bulanan berdasarkan parameter tahun operasional.
- **Project Profitability Drill-Down (`/cashflow/project/{projectId}`)**: Analisis keuangan mendalam per proyek untuk memantau profitabilitas dan performa finansial tiap unit kerja.
- **Financial Health Indicators (`/dashboard/financial-indicators`)**: Evaluasi indikator kesehatan finansial perusahaan secara otomatis.
- **Secure Executive RBAC**: Pembatasan akses ketat berbasis *Bearer Token* yang memastikan hanya jajaran eksekutif yang berhak mengakses laporan finansial.

---

## 🛠️ Spesifikasi Teknis & Prasyarat

- **Bahasa & Framework**: PHP 8.4 & Laravel 13
- **Database**: PostgreSQL / MySQL
- **Autentikasi**: Bearer Token (Integrasi dengan Modul F-1 & Validasi Eksekutif)
- **Testing**: Laravel Feature Tests (`DashboardApiTest`)

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
3. **Migrasi dan Seed Data Demo**
```bash
   php artisan migrate:fresh --seed
```
4. **Jalankan Server Lokal**
```bash
   php artisan serve
```

---

## 📡 Daftar Endpoint API Utama

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


