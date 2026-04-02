<div align="center">

# 💰 Smart Budgeting, Saving & Investment App

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v4-FDAE4B?style=for-the-badge&logo=filament&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)

**Aplikasi manajemen keuangan pribadi lengkap dengan fitur tracking investasi saham.**

Dirancang untuk membantu pengguna melacak pendapatan, pengeluaran, tabungan, anggaran, dan portofolio investasi secara _real-time_. Dibangun dengan **Laravel 12** dan **Filament v4**, menawarkan antarmuka modern, responsif, dan kaya fitur.

[🚀 Fitur](#-fitur-utama) •
[📦 Instalasi](#-instalasi--setup) •
[🏗️ Arsitektur](#-arsitektur-sistem) •
[📊 Screenshot](#-tampilan-aplikasi) •
[🤝 Kontribusi](#-kontribusi)

</div>

---

## 📋 Daftar Isi

- [✨ Fitur Utama](#-fitur-utama)
- [🛠️ Tech Stack](#️-tech-stack)
- [📁 Struktur Proyek](#-struktur-proyek)
- [🏗️ Arsitektur Sistem](#️-arsitektur-sistem)
- [📦 Instalasi & Setup](#-instalasi--setup)
- [⚙️ Konfigurasi](#️-konfigurasi)
- [🎯 Panduan Penggunaan](#-panduan-penggunaan)
- [🧠 Logic Flow & Otomatisasi](#-logic-flow--otomatisasi)
- [📊 Tampilan Aplikasi](#-tampilan-aplikasi)
- [🤝 Kontribusi](#-kontribusi)

---

## ✨ Fitur Utama

Aplikasi ini mencakup seluruh aspek manajemen keuangan pribadi dan investasi:

<table>
<tr>
<td width="50%">

### 🏦 Multi-Account Management

Kelola berbagai sumber dana (Tunai, Bank, E-Wallet) dengan saldo terlacak terpisah namun terintegrasi dalam total kekayaan.

**Fitur:**

- ✅ Unlimited akun per user
- ✅ Tipe: Cash, Bank, E-Wallet
- ✅ Saldo otomatis tersinkronisasi
- ✅ Color coding untuk identifikasi mudah

</td>
<td width="50%">

### 💸 Transaksi & Transfer

Pencatatan lengkap dengan bukti transaksi dan mutasi antar akun.

**Fitur:**

- ✅ Income & Expense tracking
- ✅ Transfer antar akun (tidak mempengaruhi laporan P/L)
- ✅ Upload bukti/struk transaksi
- ✅ Filter & search lengkap

</td>
</tr>
<tr>
<td width="50%">

### 🎯 Budgeting (Anggaran)

Kontrol pengeluaran dengan limit per kategori.

**Fitur:**

- ✅ Budget per kategori
- ✅ Progress bar real-time
- ✅ Status: Aman 🟢 / Waspada 🟡 / Overbudget 🔴
- ✅ Periode: Bulanan

</td>
<td width="50%">

### 🐖 Saving Goals (Tabungan)

Tabung untuk tujuan spesifik dengan tracking progress.

**Fitur:**

- ✅ Target amount & deadline
- ✅ Progress tracking otomatis
- ✅ Pisahkan uang bebas vs tabungan
- ✅ Multiple saving goals

</td>
</tr>
<tr>
<td width="50%">

### 🔄 Recurring Transactions

Otomatisasi tagihan rutin tanpa input manual.

**Fitur:**

- ✅ Frekuensi: Harian, Mingguan, Bulanan, Tahunan
- ✅ Auto-create via scheduler
- ✅ End date opsional
- ✅ Aktivasi/nonaktifkan fleksibel

</td>
<td width="50%">

### 📈 Investasi Saham (NEW!)

Kelola portofolio saham dengan integrasi Yahoo Finance API.

**Fitur:**

- ✅ Portfolio tracking (lot & lembar)
- ✅ Buy/Sell/Dividend transactions
- ✅ Auto-fetch harga dari Yahoo Finance
- ✅ Unrealized Gain/Loss calculation
- ✅ Weighted average price

</td>
</tr>
</table>

### 📊 Interactive Dashboard

Pusat kendali keuangan dengan visualisasi lengkap:

| Widget                   | Deskripsi                                                |
| ------------------------ | -------------------------------------------------------- |
| 📈 **Advanced Stats**    | Total Net Worth, Income/Expense bulan ini, Net Cash Flow |
| 📉 **Cash Flow Chart**   | Grafik arus kas harian                                   |
| 🥧 **Expense Pie Chart** | Komposisi pengeluaran per kategori                       |
| 💳 **Account Balance**   | Live balance semua akun                                  |
| 📊 **Budget Progress**   | Status anggaran per kategori                             |

---

## 🛠️ Tech Stack

### Backend

| Technology                                                                                                            | Version | Deskripsi                   |
| --------------------------------------------------------------------------------------------------------------------- | ------- | --------------------------- |
| ![PHP](https://img.shields.io/badge/-PHP-777BB4?style=flat-square&logo=php&logoColor=white)                           | 8.2+    | Runtime utama               |
| ![Laravel](https://img.shields.io/badge/-Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)               | 12.x    | Backend framework           |
| ![Filament](https://img.shields.io/badge/-Filament-FDAE4B?style=flat-square&logo=data:image/svg+xml;base64,PHN2Zy...) | 4.x     | Admin panel & UI components |
| ![MySQL](https://img.shields.io/badge/-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)                     | 8.0+    | Primary database            |

### Frontend

| Technology                                                                                                        | Version | Deskripsi                |
| ----------------------------------------------------------------------------------------------------------------- | ------- | ------------------------ |
| ![Tailwind](https://img.shields.io/badge/-Tailwind_CSS-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white) | 4.x     | Utility-first CSS        |
| ![Alpine.js](https://img.shields.io/badge/-Alpine.js-8BC0D0?style=flat-square&logo=alpinedotjs&logoColor=white)   | 3.x     | Lightweight JS framework |
| ![Vite](https://img.shields.io/badge/-Vite-646CFF?style=flat-square&logo=vite&logoColor=white)                    | 7.x     | Build tool & dev server  |
| ![Heroicons](https://img.shields.io/badge/-Heroicons-6366F1?style=flat-square&logo=heroicons&logoColor=white)     | -       | Icon library             |

### External Services

| Service                  | Deskripsi                        |
| ------------------------ | -------------------------------- |
| 📊 **Yahoo Finance API** | Real-time stock price data (IDX) |

---

## 📁 Struktur Proyek

```
budgeting-app/
├── 📂 app/
│   ├── 📂 Console/
│   │   └── 📂 Commands/
│   │       ├── 📄 ProcessRecurringTransactions.php  # Scheduler recurring
│   │       ├── 📄 UpdateStockPrices.php             # Update harga saham
│   │       ├── 📄 AccountsRecalculate.php           # Recalculate saldo
│   │       └── 📄 InspectLatestTransaction.php      # Debug helper
│   │
│   ├── 📂 Filament/
│   │   ├── 📂 Pages/
│   │   │   └── 📄 Dashboard.php                     # Custom dashboard
│   │   │
│   │   ├── 📂 Resources/                            # CRUD Resources
│   │   │   ├── 📂 Accounts/                         # 💳 Manajemen Akun
│   │   │   ├── 📂 Budgets/                          # 🎯 Anggaran
│   │   │   ├── 📂 Categories/                       # 🏷️ Kategori
│   │   │   ├── 📂 RecurringTransactions/            # 🔄 Transaksi Berulang
│   │   │   ├── 📂 SavingGoals/                      # 🐖 Target Tabungan
│   │   │   ├── 📂 SavingTransactions/               # 💰 Transaksi Tabungan
│   │   │   ├── 📂 StockHoldings/                    # 📈 Portofolio Saham
│   │   │   ├── 📂 StockTransactions/                # 📊 Transaksi Saham
│   │   │   ├── 📂 Transactions/                     # 💸 Transaksi Utama
│   │   │   └── 📂 Transfers/                        # ↔️ Transfer Antar Akun
│   │   │
│   │   └── 📂 Widgets/                              # Dashboard Widgets
│   │       ├── 📄 AdvancedStatsOverview.php         # Net Worth, Income, Expense
│   │       ├── 📄 StatsOverview.php                 # Quick stats
│   │       ├── 📄 CashFlowChart.php                 # Grafik arus kas
│   │       ├── 📄 ExpenseCategoryChart.php          # Pie chart pengeluaran
│   │       └── 📄 AccountBalanceTable.php           # Tabel saldo akun
│   │
│   ├── 📂 Models/                                   # Eloquent Models
│   │   ├── 📄 Account.php                           # 💳 Akun/Dompet
│   │   ├── 📄 Budget.php                            # 🎯 Anggaran
│   │   ├── 📄 Category.php                          # 🏷️ Kategori (Income/Expense)
│   │   ├── 📄 RecurringTransaction.php              # 🔄 Template Transaksi Berulang
│   │   ├── 📄 SavingGoal.php                        # 🐖 Target Tabungan
│   │   ├── 📄 SavingTransaction.php                 # 💰 Setoran ke Tabungan
│   │   ├── 📄 StockHolding.php                      # 📈 Kepemilikan Saham
│   │   ├── 📄 StockTransaction.php                  # 📊 Buy/Sell/Dividend Saham
│   │   ├── 📄 Transaction.php                       # 💸 Transaksi Utama
│   │   ├── 📄 Transfer.php                          # ↔️ Transfer Antar Akun
│   │   └── 📄 User.php                              # 👤 User Authentication
│   │
│   ├── 📂 Observers/                                # Event Observers (Auto Sync)
│   │   ├── 📄 AccountObserver.php                   # Watch account changes
│   │   ├── 📄 TransactionObserver.php               # Auto-update saldo on transaction
│   │   ├── 📄 TransferObserver.php                  # Auto-update saldo on transfer
│   │   ├── 📄 SavingTransactionObserver.php         # Auto-update saving goal
│   │   └── 📄 StockTransactionObserver.php          # Auto-update portfolio & saldo
│   │
│   ├── 📂 Services/
│   │   └── 📄 YahooFinanceService.php               # 📊 Yahoo Finance API Integration
│   │
│   └── 📂 Providers/
│       └── 📄 AppServiceProvider.php                # Register observers
│
├── 📂 database/
│   ├── 📂 migrations/                               # Database schema
│   │   ├── 📄 create_users_table.php
│   │   ├── 📄 create_categories_table.php
│   │   ├── 📄 create_accounts_table.php
│   │   ├── 📄 create_transactions_table.php
│   │   ├── 📄 create_budgets_table.php
│   │   ├── 📄 create_saving_goals_table.php
│   │   ├── 📄 create_saving_transactions_table.php
│   │   ├── 📄 create_transfers_table.php
│   │   ├── 📄 create_recurring_transactions_table.php
│   │   ├── 📄 create_stock_holdings_table.php
│   │   └── 📄 create_stock_transactions_table.php
│   │
│   └── 📂 seeders/
│       ├── 📄 DatabaseSeeder.php                    # Main seeder
│       ├── 📄 CategoriesSeeder.php                  # Default categories
│       └── 📄 AccountSeeder.php                     # Default account
│
├── 📂 resources/
│   ├── 📂 css/                                      # Stylesheets
│   ├── 📂 js/                                       # JavaScript
│   └── 📂 views/                                    # Blade templates
│
├── 📂 routes/
│   ├── 📄 web.php                                   # Web routes
│   └── 📄 console.php                               # Console commands schedule
│
├── 📂 config/                                       # Configuration files
├── 📂 public/                                       # Public assets
├── 📂 storage/                                      # Uploads & logs
├── 📂 tests/                                        # PHPUnit tests
│
├── 📄 composer.json                                 # PHP dependencies
├── 📄 package.json                                  # Node dependencies
├── 📄 vite.config.js                                # Vite configuration
├── 📄 tailwind.config.js                            # Tailwind configuration
└── 📄 .env.example                                  # Environment template
```

---

## 🏗️ Arsitektur Sistem

### 📊 Entity Relationship Diagram (ERD)

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    User     │       │   Account   │       │  Category   │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id          │◄──┬───│ id          │       │ id          │
│ name        │   │   │ user_id     │───────│ user_id     │
│ email       │   │   │ name        │       │ name        │
│ password    │   │   │ type        │       │ type        │ (income/expense)
└─────────────┘   │   │ color       │       │ color       │
                  │   │ initial_bal │       │ icon        │
                  │   │ current_bal │       └──────┬──────┘
                  │   └──────┬──────┘              │
                  │          │                     │
                  │          ▼                     ▼
                  │   ┌─────────────┐       ┌─────────────┐
                  │   │ Transaction │       │   Budget    │
                  │   ├─────────────┤       ├─────────────┤
                  │   │ id          │       │ id          │
                  │   │ user_id     │───────│ user_id     │
                  │   │ account_id  │       │ category_id │
                  │   │ category_id │       │ amount      │
                  │   │ date        │       │ period      │
                  │   │ amount      │       └─────────────┘
                  │   │ description │
                  │   │ image       │
                  │   └─────────────┘
                  │
    ┌─────────────┼─────────────┬─────────────────┐
    │             │             │                 │
    ▼             ▼             ▼                 ▼
┌─────────┐ ┌──────────┐ ┌───────────┐   ┌─────────────┐
│Transfer │ │Saving    │ │Recurring  │   │StockHolding │
├─────────┤ │Goal      │ │Transaction│   ├─────────────┤
│from_acc │ ├──────────┤ ├───────────┤   │ ticker      │
│to_acc   │ │name      │ │frequency  │   │ total_shares│ (LOT)
│amount   │ │target_amt│ │next_run   │   │ avg_price   │
│date     │ │current   │ │is_active  │   │ current_prc │
└─────────┘ │status    │ └───────────┘   └──────┬──────┘
            └────┬─────┘                        │
                 │                              │
                 ▼                              ▼
          ┌────────────┐               ┌───────────────┐
          │Saving      │               │Stock          │
          │Transaction │               │Transaction    │
          ├────────────┤               ├───────────────┤
          │goal_id     │               │ holding_id    │
          │account_id  │               │ account_id    │
          │amount      │               │ type          │ (buy/sell/dividend)
          │date        │               │ shares (LOT)  │
          └────────────┘               │ price         │
                                       │ fee           │
                                       └───────────────┘
```

### 🔄 Data Flow Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                            │
│                    (Filament Admin Panel)                        │
└───────────────────────────┬──────────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────────┐
│                      FILAMENT RESOURCES                          │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐    │
│  │ Accounts   │ │Transactions│ │  Budgets   │ │   Stocks   │    │
│  │  Resource  │ │  Resource  │ │  Resource  │ │  Resource  │    │
│  └─────┬──────┘ └─────┬──────┘ └─────┬──────┘ └─────┬──────┘    │
└────────┼──────────────┼──────────────┼──────────────┼────────────┘
         │              │              │              │
         ▼              ▼              ▼              ▼
┌──────────────────────────────────────────────────────────────────┐
│                      ELOQUENT MODELS                             │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐    │
│  │  Account   │ │Transaction │ │   Budget   │ │StockHolding│    │
│  │   Model    │ │   Model    │ │   Model    │ │   Model    │    │
│  └─────┬──────┘ └─────┬──────┘ └─────┬──────┘ └─────┬──────┘    │
└────────┼──────────────┼──────────────┼──────────────┼────────────┘
         │              │              │              │
         ▼              ▼              ▼              ▼
┌──────────────────────────────────────────────────────────────────┐
│                      MODEL OBSERVERS                             │
│         (Auto-sync balance on create/update/delete)              │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │  Transaction    │  │    Transfer     │  │StockTransaction │  │
│  │    Observer     │  │    Observer     │  │    Observer     │  │
│  │                 │  │                 │  │                 │  │
│  │ • Update saldo  │  │ • Debit from    │  │ • Update shares │  │
│  │   on income     │  │ • Credit to     │  │ • Update avg    │  │
│  │ • Deduct saldo  │  │ • Atomic op     │  │ • Update saldo  │  │
│  │   on expense    │  │                 │  │                 │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
└──────────────────────────────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────────────────────┐
│                        DATABASE                                  │
│                    (MySQL / MariaDB)                             │
└──────────────────────────────────────────────────────────────────┘
```

---

## 📦 Instalasi & Setup

### 📋 Prasyarat

Pastikan sistem Anda memiliki:

| Requirement | Version | Cek Instalasi     |
| ----------- | ------- | ----------------- |
| PHP         | ≥ 8.2   | `php -v`          |
| Composer    | Latest  | `composer -V`     |
| Node.js     | ≥ 18.x  | `node -v`         |
| NPM         | ≥ 9.x   | `npm -v`          |
| MySQL       | ≥ 8.0   | `mysql --version` |

### 🚀 Quick Start

```bash
# 1. Clone repository
git clone https://github.com/username/budgeting-app.git
cd budgeting-app

# 2. Install semua dependencies sekaligus
composer setup
```

Script `composer setup` akan otomatis menjalankan:

- ✅ `composer install` - Install PHP dependencies
- ✅ Copy `.env.example` ke `.env`
- ✅ `php artisan key:generate` - Generate app key
- ✅ `php artisan migrate` - Migrate database
- ✅ `npm install` - Install Node dependencies
- ✅ `npm run build` - Build frontend assets

### 📝 Manual Installation

Jika prefer setup manual:

```bash
# 1. Clone repository
git clone https://github.com/username/budgeting-app.git
cd budgeting-app

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Konfigurasi environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di .env
# Edit file .env dengan text editor

# 7. Migrasi & seed database
php artisan migrate --seed

# 8. Link storage
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. Jalankan server
php artisan serve
```

### 🔐 Default Login

Setelah seeding, gunakan kredensial berikut:

| Field        | Value             |
| ------------ | ----------------- |
| **Email**    | `admin@gmail.com` |
| **Password** | `123456`          |

> ⚠️ **Penting:** Ganti password setelah login pertama!

---

## ⚙️ Konfigurasi

### 📄 Environment Variables

Konfigurasi utama di file `.env`:

```env
# Application
APP_NAME="Budgeting App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=budgeting_app
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database

# Cache
CACHE_DRIVER=database

# Queue (untuk async jobs)
QUEUE_CONNECTION=database
```

### 🕐 Scheduler (Cron Job)

Untuk fitur **Recurring Transactions** dan **Auto Update Stock Prices**, tambahkan cron job:

```bash
# Di server production (crontab -e)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 📊 Stock Price Commands

```bash
# Update harga semua saham
php artisan stocks:update-prices

# Update saham tertentu saja
php artisan stocks:update-prices --ticker=BBCA

# Dry run (preview tanpa update)
php artisan stocks:update-prices --dry-run
```

### 🔄 Recurring Transaction Command

```bash
# Process recurring transactions yang jatuh tempo
php artisan app:process-recurring
```

---

## 🎯 Panduan Penggunaan

### 1️⃣ Setup Awal

```
📌 Urutan yang disarankan:

1. Categories     → Buat kategori income/expense
2. Accounts       → Tambahkan akun (Cash, Bank, E-Wallet)
3. Transactions   → Mulai catat transaksi harian
4. Budgets        → Set limit anggaran per kategori
5. Saving Goals   → Tentukan target tabungan
6. Stock Holdings → Tambah portofolio saham (opsional)
```

### 2️⃣ Mengelola Akun (Accounts)

| Aksi      | Deskripsi                                            |
| --------- | ---------------------------------------------------- |
| ➕ Create | Tambah akun baru dengan saldo awal                   |
| 📝 Edit   | Ubah nama/tipe akun (saldo tidak bisa diedit manual) |
| 🗑️ Delete | Hapus akun (pastikan tidak ada transaksi terkait)    |

> 💡 **Tips:** Saldo `current_balance` selalu dihitung otomatis dari transaksi!

### 3️⃣ Mencatat Transaksi

```
┌─────────────────────────────────────────────────┐
│              CREATE TRANSACTION                 │
├─────────────────────────────────────────────────┤
│  📅 Date:        [  2024-01-15  ]               │
│  💳 Account:     [ BCA ▼ ]                      │
│  🏷️ Category:    [ Makanan ▼ ]                  │
│  💰 Amount:      [  150000  ]                   │
│  📝 Description: [ Makan siang ]                │
│  📎 Receipt:     [ Upload Image ]               │
│                                                 │
│            [ Cancel ]  [ Save ]                 │
└─────────────────────────────────────────────────┘

✅ Saldo BCA otomatis berkurang Rp 150.000
```

### 4️⃣ Transfer Antar Akun

```
┌─────────────────────────────────────────────────┐
│               CREATE TRANSFER                   │
├─────────────────────────────────────────────────┤
│  📅 Date:        [  2024-01-15  ]               │
│  💳 From:        [ BCA ▼ ]                      │
│  💳 To:          [ GoPay ▼ ]                    │
│  💰 Amount:      [  500000  ]                   │
│  📝 Notes:       [ Top up GoPay ]               │
│                                                 │
│            [ Cancel ]  [ Save ]                 │
└─────────────────────────────────────────────────┘

✅ Saldo BCA   -Rp 500.000
✅ Saldo GoPay +Rp 500.000
ℹ️ Tidak mempengaruhi laporan Income/Expense
```

### 5️⃣ Mengelola Investasi Saham

```
┌─────────────────────────────────────────────────┐
│        STOCK PORTFOLIO MANAGEMENT               │
├─────────────────────────────────────────────────┤
│                                                 │
│  📈 Step 1: Buat Stock Holding                  │
│     └─ Masukkan kode saham (BBCA, TLKM, dll)    │
│                                                 │
│  💰 Step 2: Catat Transaksi Saham               │
│     └─ BUY:  Beli saham (potong saldo akun)     │
│     └─ SELL: Jual saham (tambah saldo akun)     │
│     └─ DIVIDEND: Terima dividen                 │
│                                                 │
│  🔄 Step 3: Update Harga                        │
│     └─ Klik "Ambil Harga" untuk fetch dari      │
│        Yahoo Finance (BEI/IDX stocks)           │
│                                                 │
└─────────────────────────────────────────────────┘

📊 Catatan Unit Saham:
   • 1 Lot = 100 lembar saham
   • Input transaksi dalam LOT
   • Sistem otomatis konversi ke lembar untuk kalkulasi
```

---

## 🧠 Logic Flow & Otomatisasi

### 🔄 Observer Pattern (Auto Balance Sync)

Sistem menggunakan **Laravel Model Observers** untuk menjaga integritas saldo:

```
┌────────────────────────────────────────────────────────────────┐
│                    TRANSACTION OBSERVER                        │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  📥 CREATED:                                                   │
│     ├─ Income  → Account.current_balance += amount             │
│     └─ Expense → Account.current_balance -= amount             │
│                                                                │
│  ✏️ UPDATED:                                                   │
│     ├─ Revert old: Kembalikan saldo ke kondisi sebelum edit    │
│     └─ Apply new:  Terapkan perubahan baru                     │
│                                                                │
│  🗑️ DELETED:                                                   │
│     ├─ Income  → Account.current_balance -= amount             │
│     └─ Expense → Account.current_balance += amount             │
│                                                                │
└────────────────────────────────────────────────────────────────┘
```

### 📈 Stock Transaction Observer

```
┌────────────────────────────────────────────────────────────────┐
│               STOCK TRANSACTION OBSERVER                       │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  🟢 BUY Transaction:                                           │
│     1. Validate saldo akun mencukupi                           │
│     2. Deduct: Account -= (lot × 100 × price) + fee            │
│     3. Recalculate weighted average price                      │
│     4. Update total_shares di StockHolding                     │
│                                                                │
│  🔴 SELL Transaction:                                          │
│     1. Validate lot mencukupi                                  │
│     2. Credit: Account += (lot × 100 × price) - fee            │
│     3. Decrease total_shares di StockHolding                   │
│     4. Average price tetap (tidak berubah saat jual)           │
│                                                                │
│  💵 DIVIDEND:                                                  │
│     1. Credit: Account += dividend_amount                      │
│     2. Tidak mempengaruhi shares atau average price            │
│                                                                │
└────────────────────────────────────────────────────────────────┘
```

### ⏰ Scheduler Commands

```
┌────────────────────────────────────────────────────────────────┐
│                    SCHEDULED TASKS                             │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  🔄 app:process-recurring (Daily)                              │
│     │                                                          │
│     ├─ Query: SELECT * FROM recurring_transactions             │
│     │         WHERE is_active = true                           │
│     │         AND next_run_date <= TODAY                       │
│     │                                                          │
│     ├─ For each due recurring:                                 │
│     │   1. Create real Transaction                             │
│     │   2. Update next_run_date berdasarkan frequency          │
│     │   3. Jika next_run > end_date → deactivate               │
│     │                                                          │
│     └─ Log: "Processed {count} transaction(s)"                 │
│                                                                │
│  📊 stocks:update-prices (Manual/Scheduled)                    │
│     │                                                          │
│     ├─ Fetch semua ticker unik dari stock_holdings             │
│     │                                                          │
│     ├─ For each ticker:                                        │
│     │   1. Call Yahoo Finance API ({TICKER}.JK)                │
│     │   2. Parse regularMarketPrice                            │
│     │   3. Update current_price di database                    │
│     │   4. Rate limit: 300ms delay antar request               │
│     │                                                          │
│     └─ Output: Table hasil update + summary                    │
│                                                                │
└────────────────────────────────────────────────────────────────┘
```

### 🧮 Kalkulasi Portofolio Saham

```
┌────────────────────────────────────────────────────────────────┐
│              STOCK CALCULATION FORMULAS                        │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  📊 Total Lembar = total_shares × 100                          │
│                                                                │
│  💰 Total Cost (Modal) = average_price × total_lembar          │
│                                                                │
│  📈 Market Value = current_price × total_lembar                │
│                                                                │
│  📉 Unrealized Gain/Loss = Market Value - Total Cost           │
│     = (current_price - average_price) × total_lembar           │
│                                                                │
│  🔄 Weighted Average Price (saat BUY):                         │
│     new_avg = (old_avg × old_lot + new_price × new_lot)        │
│               ────────────────────────────────────────         │
│                        old_lot + new_lot                       │
│                                                                │
└────────────────────────────────────────────────────────────────┘

📝 Contoh:
   Existing: 10 lot @ Rp 8.000 (avg)
   Buy:       5 lot @ Rp 8.500

   New Avg = (8000×10 + 8500×5) / (10+5)
           = (80000 + 42500) / 15
           = Rp 8.166,67
```

---

## 📊 Tampilan Aplikasi

### 🏠 Dashboard Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│  🏠 Dashboard                                                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐   │
│  │ 🏦 Net Worth│ │ 📈 Income   │ │ 📉 Expense  │ │ 💰 Cashflow │   │
│  │  Rp 50.5 M  │ │  Rp 8.5 M   │ │  Rp 3.2 M   │ │  Rp 5.3 M   │   │
│  │  All accounts│ │  This month │ │  This month │ │  Savings    │   │
│  └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘   │
│                                                                     │
│  ┌───────────────────────────────┐ ┌───────────────────────────┐   │
│  │   📊 Cash Flow Chart          │ │   🥧 Expense by Category  │   │
│  │                               │ │                           │   │
│  │   ▓▓▓▓▓▓▓▓░░░░░░░░           │ │     🍔 Makanan    35%     │   │
│  │   ▓▓▓▓▓▓▓▓▓▓░░░░░░           │ │     🚗 Transport  25%     │   │
│  │   ▓▓▓▓▓▓░░░░░░░░░░           │ │     🏠 Housing    20%     │   │
│  │   ▓▓▓▓▓▓▓▓▓░░░░░░░           │ │     🎮 Hiburan    10%     │   │
│  │   ─────────────────           │ │     📱 Others     10%     │   │
│  │   1  5  10  15  20  25  30    │ │                           │   │
│  └───────────────────────────────┘ └───────────────────────────┘   │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │   💳 Account Balances                                        │   │
│  │   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │   │
│  │   💵 Cash              Rp  2.500.000                         │   │
│  │   🏦 BCA               Rp 25.000.000                         │   │
│  │   🏦 Mandiri           Rp 15.000.000                         │   │
│  │   📱 GoPay             Rp  3.000.000                         │   │
│  │   📱 OVO               Rp  5.000.000                         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 📈 Stock Portfolio View

```
┌─────────────────────────────────────────────────────────────────────┐
│  📈 Portofolio Saham                                    [+ New]     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────┬───────┬──────────┬──────────┬──────────┬─────────────┐   │
│  │Ticker│ Lot   │ Avg Price│ Current  │ Mkt Value│ Gain/Loss   │   │
│  ├──────┼───────┼──────────┼──────────┼──────────┼─────────────┤   │
│  │ BBCA │  50   │ Rp 8.150 │ Rp 9.500 │ Rp47.5 M │ 🟢 +Rp6.75M │   │
│  │ TLKM │ 100   │ Rp 3.200 │ Rp 3.100 │ Rp31.0 M │ 🔴 -Rp1.00M │   │
│  │ BMRI │  30   │ Rp 5.500 │ Rp 5.800 │ Rp17.4 M │ 🟢 +Rp900K  │   │
│  │ ASII │  20   │ Rp 4.800 │ Rp 4.750 │ Rp 9.5 M │ 🔴 -Rp100K  │   │
│  └──────┴───────┴──────────┴──────────┴──────────┴─────────────┘   │
│                                                                     │
│  Total Portfolio Value: Rp 105.4 M                                  │
│  Total Unrealized P/L:  🟢 +Rp 6.55 M (+6.6%)                       │
│                                                                     │
│  Actions: [🔄 Refresh All] [📊 Export] [📈 Add Stock]               │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=TransactionObserverTest
```

---

## 🚀 Deployment

### Development Server

```bash
# Concurrent mode (recommended)
composer dev

# Akan menjalankan:
# - php artisan serve     → Laravel server
# - php artisan queue:listen → Queue worker
# - php artisan pail      → Log viewer
# - npm run dev           → Vite dev server
```

### Production Build

```bash
# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache

# Run migrations
php artisan migrate --force
```

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Berikut cara berkontribusi:

```bash
# 1. Fork repository ini

# 2. Clone fork Anda
git clone https://github.com/YOUR_USERNAME/budgeting-app.git

# 3. Buat branch fitur baru
git checkout -b feature/fitur-keren

# 4. Commit perubahan
git add .
git commit -m "feat: tambah fitur keren"

# 5. Push ke branch
git push origin feature/fitur-keren

# 6. Buat Pull Request
```

### 📌 Contribution Guidelines

- 📝 Gunakan [Conventional Commits](https://www.conventionalcommits.org/)
- ✅ Pastikan semua test passing
- 🎨 Ikuti code style yang ada (PSR-12)
- 📖 Update dokumentasi jika diperlukan

### 💡 Ide Fitur yang Bisa Dikerjakan

- [ ] 📊 Export laporan ke PDF/Excel
- [ ] 🔔 Notifikasi budget warning
- [ ] 📱 PWA support
- [ ] 🌍 Multi-currency support
- [ ] 📈 Integrasi dengan bank API
- [ ] 📊 Advanced analytics & reports
- [ ] 🤖 AI spending insights

---

## 📞 Support

Jika menemukan bug atau ada pertanyaan:

- 🐛 [Buka Issue](https://github.com/SulthanRaghib/budgeting-app/issues)
- 💬 [Diskusi](https://github.com/SulthanRaghib/budgeting-app/discussions)

---

<div align="center">

**Dibuat dengan ❤️ menggunakan Laravel & Filament**

⭐ Star this repo jika bermanfaat!

</div>
