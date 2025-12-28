# 💰 Smart Budgeting & Saving App

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-v3-FAA029?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-005C84?style=for-the-badge&logo=mysql)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)

Sebuah aplikasi manajemen keuangan pribadi yang komprehensif, dirancang untuk membantu pengguna melacak pendapatan, pengeluaran, tabungan, dan anggaran secara _real-time_. Dibangun menggunakan **Laravel** dan **Filament PHP**, aplikasi ini menawarkan antarmuka yang modern, responsif, dan kaya fitur.

---

## 📋 Daftar Isi

-   [Fitur Utama](#-fitur-utama)
-   [Teknologi](#-teknologi)
-   [Struktur & Modul Sistem](#-struktur--modul-sistem)
-   [Alur Logika & Otomatisasi](#-alur-logika--otomatisasi)
-   [Instalasi & Setup](#-instalasi--setup)
-   [Konfigurasi Server (Opsional)](#-konfigurasi-server-opsional)
-   [Kontribusi](#-kontribusi)

---

## 🚀 Fitur Utama

Aplikasi ini mencakup seluruh aspek manajemen keuangan pribadi:

### 1. 🏦 Multi-Account Management

Kelola berbagai "dompet" atau sumber dana (misal: Tunai, BCA, GoPay, OVO). Saldo setiap akun terlacak secara terpisah namun terintegrasi dalam laporan total kekayaan (_Net Worth_).

### 2. 💸 Transaksi & Transfer

-   **Income & Expense:** Pencatatan pemasukan dan pengeluaran harian dengan kategori spesifik.
-   **Transfer:** Fitur pindah buku antar akun (misal: Tarik Tunai ATM) tanpa merusak laporan Income/Expense.
-   **Bukti Transaksi:** Upload foto struk/nota untuk arsip.

### 3. 🎯 Budgeting (Anggaran)

Tetapkan batas pengeluaran bulanan per kategori.

-   Visualisasi _Progress Bar_ (Target vs Realisasi).
-   Indikator status: _Aman_ (Hijau), _Waspada_ (Kuning), _Overbudget_ (Merah).

### 4. 🐖 Saving Goals (Tabungan Impian)

Fitur untuk menabung demi tujuan tertentu (misal: Beli Laptop).

-   Progress bar otomatis berdasarkan setoran.
-   Memisahkan "Uang Bebas" dan "Uang Tabungan" agar tidak terpakai belanja.

### 5. 🔄 Recurring Transactions (Otomatisasi)

Jangan pernah input manual tagihan rutin lagi.

-   Jadwalkan transaksi (Harian, Mingguan, Bulanan, Tahunan).
-   Sistem akan otomatis membuat transaksi saat tanggal jatuh tempo tiba (via Scheduler).

### 6. 📊 Interactive Dashboard

Pusat kendali keuangan Anda:

-   **Statistik Cepat:** Total Saldo, Income vs Expense bulan ini.
-   **Grafik Arus Kas:** Tren pengeluaran harian.
-   **Komposisi Pengeluaran:** Pie chart per kategori.
-   **Tabel Saldo:** Live balance semua akun bank/e-wallet.

---

## 🛠 Teknologi

-   **Backend Framework:** Laravel 12
-   **Admin Panel / UI:** Filament PHP v4
-   **Database:** MySQL / MariaDB
-   **Frontend Assets:** Tailwind CSS, Alpine.js (TALL Stack)
-   **Icons:** Heroicons

---

## 🧩 Struktur & Modul Sistem

Berikut adalah penjelasan mendalam mengenai modul-modul yang ada di dalam aplikasi:

### A. 💳 Accounts (Dompet)

Modul ini adalah fondasi sistem (_Single Source of Truth_ untuk saldo).

-   **Field:** Nama Bank, Tipe (Cash/Bank/E-Wallet), Saldo Awal, Saldo Saat Ini.
-   **Logika:** Saldo tidak diubah manual, melainkan hasil kalkulasi otomatis dari Transaksi & Transfer.

### B. 🏷️ Categories (Kategori)

Pengelompokan transaksi agar laporan rapi.

-   **Tipe:** Income (Pemasukan) atau Expense (Pengeluaran).
-   **Visual:** Dilengkapi warna dan ikon untuk grafik dashboard.

### C. 💸 Transactions (Buku Kas)

Jantung pencatatan keuangan.

-   Setiap transaksi **wajib** memilih Akun (Sumber Dana) dan Kategori.
-   Menggunakan **Observer** untuk langsung memotong/menambah saldo Akun terkait.

### D. ⚖️ Budgets (Anggaran)

Alat kontrol pengeluaran.

-   Budget dipasang pada Kategori tertentu (misal: Makanan = 1 Juta/bulan).
-   Sistem menghitung total transaksi kategori tersebut di bulan berjalan dan membandingkannya dengan limit budget.

### E. 🔁 Transfers (Mutasi)

Memindahkan uang tanpa dianggap sebagai pengeluaran hangus.

-   Logika: Mengurangi saldo _Akun Asal_ dan menambah saldo _Akun Tujuan_ secara bersamaan (Atomic Operation).

### F. 🏆 Saving Goals & Transactions

-   **Saving Goal:** Wadah target (Target Amount).
-   **Saving Transaction:** Aksi memindahkan uang dari Akun Utama ke dalam Goal. Ini mengurangi saldo "siap pakai" di Akun, tapi menambah aset di Goal.

---

## 🧠 Alur Logika & Otomatisasi

Sistem ini menggunakan **Model Observers** dan **Scheduled Commands** untuk menjaga integritas data.

### 1. Sinkronisasi Saldo Otomatis (Observer Pattern)

Anda tidak perlu menghitung saldo manual. Sistem memiliki `AccountObserver`, `TransactionObserver`, dan `TransferObserver`.

-   Saat **Transaksi Baru** dibuat ➝ Saldo Akun otomatis terupdate.
-   Saat **Transaksi Diedit** ➝ Saldo lama dikembalikan, saldo baru diterapkan.
-   Saat **Transaksi Dihapus** ➝ Saldo dikembalikan ke kondisi semula.

### 2. Recurring Engine (Scheduler)

Terdapat _Console Command_ khusus: `app:process-recurring-transactions`.

-   Berjalan setiap hari (via Cron Job).
-   Mengecek tabel `recurring_transactions`.
-   Jika hari ini = `next_run_date`, sistem menduplikasi data menjadi transaksi nyata dan menjadwalkan tanggal berikutnya.

### 3. Validasi Keuangan

-   **Cegah Saldo Minus:** Sistem memvalidasi apakah saldo akun mencukupi sebelum melakukan Transfer atau Saving.

---

## 💻 Instalasi & Setup

Ikuti langkah ini untuk menjalankan project di lokal (Localhost):

### Prasyarat

-   PHP 8.2 atau lebih baru.
-   Composer.
-   Node.js & NPM.
-   MySQL Database.

### Langkah-langkah

1.  **Clone Repository**

    ```bash
    git clone [https://github.com/username/budgeting-app.git](https://github.com/username/budgeting-app.git)
    cd budgeting-app
    ```

2.  **Install Dependensi Backend**

    ```bash
    composer install
    ```

3.  **Install Dependensi Frontend**

    ```bash
    npm install && npm run build
    ```

4.  **Konfigurasi Environment**
    Duplikat file `.env.example` menjadi `.env`:

    ```bash
    cp .env.example .env
    ```

    Buka file `.env` dan atur koneksi database:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate Key & Migrasi Database**

    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```

    _(Command `--seed` akan membuat User Admin default, Akun Cash, dan Kategori standar)._

6.  **Setup Storage Link**
    Agar gambar/bukti transaksi bisa diakses publik:

    ```bash
    php artisan storage:link
    ```

7.  **Jalankan Server**
    ```bash
    php artisan serve
    ```
    Akses aplikasi di: `http://127.0.0.1:8000/admin`

---

## ⚙️ Konfigurasi Server (Opsional)

### Menjalankan Recurring Transactions (Cron Job)

Jika di-deploy ke server (VPS/Hosting), tambahkan Cron Job berikut agar fitur transaksi berulang berjalan otomatis:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

```

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Jika Anda ingin menambahkan fitur (misal: Laporan PDF, Integrasi Bank API), silakan:

1. Fork repository ini.
2. Buat branch fitur baru (`git checkout -b fitur-keren`).
3. Commit perubahan Anda.
4. Push ke branch tersebut.
5. Buat Pull Request.

---

<p align="center">
Dibuat dengan ❤️ menggunakan <strong>Laravel</strong> & <strong>Filament</strong>
</p>

tes
