<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BudgetKu - Kelola Keuangan Cerdas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

<body class="bg-slate-900 text-white">

    <!-- Navbar -->
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-emerald-400">💰 BudgetKu</div>
        <a href="/admin/login"
            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-full font-medium transition">Login</a>
    </nav>

    <!-- Hero Section -->
    <div class="container mx-auto px-6 py-20 text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
            Atur Keuangan,<br>
            <span class="text-emerald-400">Wujudkan Impian.</span>
        </h1>
        <p class="text-xl text-slate-400 mb-10 max-w-2xl mx-auto">
            Aplikasi pencatat keuangan simpel dengan fitur Recurring Transactions, Laporan Cashflow, dan Analisis
            Pengeluaran.
        </p>

        <div class="flex justify-center gap-4">
            <a href="/admin/login"
                class="px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition transform hover:-translate-y-1">
                Mulai Sekarang 🚀
            </a>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="bg-slate-800 py-20">
        <div class="container mx-auto px-6 grid md:grid-cols-3 gap-10">
            <div class="p-6 bg-slate-700 rounded-2xl">
                <div class="text-4xl mb-4">🔄</div>
                <h3 class="text-xl font-bold mb-2">Transaksi Berulang</h3>
                <p class="text-slate-400">Otomatis catat tagihan bulanan tanpa perlu input manual setiap saat.</p>
            </div>
            <div class="p-6 bg-slate-700 rounded-2xl">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-xl font-bold mb-2">Analisis Visual</h3>
                <p class="text-slate-400">Grafik interaktif untuk memantau arus kas dan kategori pengeluaran.</p>
            </div>
            <div class="p-6 bg-slate-700 rounded-2xl">
                <div class="text-4xl mb-4">🔒</div>
                <h3 class="text-xl font-bold mb-2">Aman & Privat</h3>
                <p class="text-slate-400">Data keuangan Anda tersimpan aman dan hanya bisa diakses oleh Anda.</p>
            </div>
        </div>
    </div>

    <footer class="text-center py-8 text-slate-500 text-sm">
        &copy; {{ date('Y') }} BudgetKu App. Built with Laravel & Filament.
    </footer>

</body>

</html>
