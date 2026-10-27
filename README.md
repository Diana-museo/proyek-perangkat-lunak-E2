<div align="center">

# 📊 E-2: Cash Flow & Executive Dashboard
### Agregator Keuangan Terpadu & Dasbor Eksekutif Perusahaan
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

Modul **E-2** bertindak sebagai *nerve center* finansial bagi PT Anaheim Nimbus Universal. Sistem ini tidak melakukan transaksi manual, melainkan berfungsi sebagai **agregator** yang menghimpun, mengolah, dan menyajikan metrik arus kas, profitabilitas proyek, serta proyeksi keuangan secara *real-time* khusus untuk kebutuhan pengambilan keputusan tingkat direksi.

---

### 🌐 Ekosistem Integrasi Lintas Modul
E-2 terhubung secara terdistribusi dengan berbagai modul hulu dalam arsitektur ERP:

* **Modul F-1 (Autentikasi & Manajemen Pengguna):** Validasi *Bearer Token*, manajemen sesi, dan kontrol hak akses berbasis peran (RBAC) untuk Direksi dan Kepala Finance.
* **Modul D-1 (Registrasi Proyek):** Sinkronisasi master data proyek, alokasi anggaran, dan status operasional.
* **Modul D-2 (Kontrak & Invoice):** Penarikan data tagihan masuk dan piutang klien.
* **Modul E-1 (Pajak & e-Faktur):** Konsumsi ringkasan kewajiban pajak keluaran melalui endpoint agregasi.
* **Modul F-5 (Payroll & Gaji):** Rekapitulasi pengeluaran operasional rutin untuk pembayaran gaji pegawai.

---

## ⚡ Fitur Unggulan

* **Executive Financial Summary**: Visualisasi metrik kesehatan perusahaan (Total Kas Masuk, Keluar, dan *Net Cash Flow*) dalam satu layar tunggal.
* **Project Profitability Drill-Down**: Analisis keuangan per proyek untuk memantau margin keuntungan dan perputaran kas tiap unit kerja.
* **Automated Data Agregator**: Penggabungan otomatis data dari modul D-1, D-2, E-1, dan F-5 dengan mekanisme *fallback mock* untuk menjaga ketersediaan sistem.
* **Secure Role-Based Access**: Pembatasan akses ketat yang memastikan hanya jajaran eksekutif (CEO, COO, Finance Manager) yang dapat melihat laporan finansial sensitif.

---

## 🛠️ Spesifikasi Teknis & Prasyarat

* **Bahasa & Framework**: PHP 8.4 & Laravel 13
* **Database**: MySQL 8.0+
* **Autentikasi**: Bearer Token (Integrasi dengan Modul F-1)

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

| Endpoint | Metode | Deskripsi |
|---|---|---|
|/cashflow/summary|GET|Ringkasan arus kas masuk dan keluar global|
|/cashflow|POST|Pencatatan transaksi kas lokal|
|/cashflow/project/{projectId}|GET|Detail arus kas berdasarkan proyek tertentu|
|/dashboard/financial-indicators|GET|Indikator keuangan manajemen dan direksi|
|/dashboard/aggregated-summary|GET|Dasbor agregat dari modul D-1, D-2, E-1, F-5|
|/pemasukan-luar-jadwal|POST|Pencatatan pemasukan manual di luar termin (BL-04)|
|/pengeluaran-operasional|POST|Pencatatan pengeluaran operasional kantor (BL-13)|


---

## 🔒 Keamanan & Penanganan Kesalahan

Sistem menerapkan standar respons JSON seragam dengan kode status HTTP yang jelas:

* 200 OK: Permintaan berhasil diproses.

* 400 Bad Request (ERR-REQ-400): Parameter query yang dikirimkan tidak valid (misalnya format taxPeriod salah atau projectId kosong).

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


