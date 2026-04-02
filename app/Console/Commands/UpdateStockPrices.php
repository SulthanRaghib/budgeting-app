<?php

namespace App\Console\Commands;

use App\Models\StockHolding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:update-prices
                            {--ticker= : Update specific ticker only}
                            {--dry-run : Show prices without updating database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stock prices from Yahoo Finance API';

    /**
     * Yahoo Finance API base URL
     */
    private const YAHOO_API_URL = 'https://query1.finance.yahoo.com/v8/finance/chart/';

    /**
     * User-Agent header to avoid 403 errors
     */
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Memulai update harga saham dari Yahoo Finance...');
        $this->newLine();

        // Get tickers to update
        $query = StockHolding::query()
            ->where('total_shares', '>', 0)
            ->select('ticker')
            ->distinct();

        // Filter by specific ticker if provided
        if ($ticker = $this->option('ticker')) {
            $query->where('ticker', strtoupper($ticker));
        }

        $tickers = $query->pluck('ticker')->toArray();

        if (empty($tickers)) {
            $this->warn('⚠️  Tidak ada saham yang perlu diupdate.');
            return Command::SUCCESS;
        }

        $this->info("📊 Ditemukan " . count($tickers) . " saham untuk diupdate.");
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        if ($isDryRun) {
            $this->warn('🔍 Mode DRY-RUN: Hanya menampilkan harga tanpa update database.');
            $this->newLine();
        }

        $progressBar = $this->output->createProgressBar(count($tickers));
        $progressBar->start();

        $successCount = 0;
        $failCount = 0;
        $results = [];

        foreach ($tickers as $ticker) {
            $progressBar->advance();

            try {
                $price = $this->fetchPriceFromYahoo($ticker);

                if ($price !== null) {
                    $results[] = [
                        'ticker' => $ticker,
                        'price' => $price,
                        'status' => 'success',
                    ];

                    if (!$isDryRun) {
                        // Update all holdings with this ticker
                        StockHolding::where('ticker', $ticker)
                            ->update(['current_price' => $price]);
                    }

                    $successCount++;
                } else {
                    $results[] = [
                        'ticker' => $ticker,
                        'price' => null,
                        'status' => 'failed',
                    ];
                    $failCount++;
                }

                // Small delay to avoid rate limiting
                usleep(300000); // 300ms delay

            } catch (\Exception $e) {
                $results[] = [
                    'ticker' => $ticker,
                    'price' => null,
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];

                Log::error("Failed to update stock price for {$ticker}", [
                    'error' => $e->getMessage(),
                ]);

                $failCount++;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results table
        $this->displayResults($results);

        // Summary
        $this->newLine();
        $this->info("✅ Berhasil: {$successCount} saham");
        if ($failCount > 0) {
            $this->error("❌ Gagal: {$failCount} saham");
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('💡 Jalankan tanpa --dry-run untuk menyimpan ke database.');
        }

        return $failCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Fetch stock price from Yahoo Finance API
     */
    private function fetchPriceFromYahoo(string $ticker): ?float
    {
        // Append .JK suffix for Indonesian stocks
        $yahooTicker = strtoupper($ticker) . '.JK';
        $url = self::YAHOO_API_URL . $yahooTicker;

        $response = Http::withHeaders([
            'User-Agent' => self::USER_AGENT,
            'Accept' => 'application/json',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])
            ->timeout(10)
            ->get($url, [
                'interval' => '1d',
                'range' => '1d',
            ]);

        if (!$response->successful()) {
            Log::warning("Yahoo Finance API error for {$ticker}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        // Parse the price from response
        // Path: chart.result[0].meta.regularMarketPrice
        $price = data_get($data, 'chart.result.0.meta.regularMarketPrice');

        if ($price === null) {
            // Try alternative path: chart.result[0].indicators.quote[0].close[-1]
            $closes = data_get($data, 'chart.result.0.indicators.quote.0.close', []);
            if (!empty($closes)) {
                // Get the last non-null close price
                $closes = array_filter($closes, fn($v) => $v !== null);
                $price = !empty($closes) ? end($closes) : null;
            }
        }

        if ($price === null) {
            Log::warning("Could not parse price for {$ticker}", [
                'response' => $data,
            ]);
            return null;
        }

        return (float) $price;
    }

    /**
     * Display results in a table format
     */
    private function displayResults(array $results): void
    {
        $tableData = [];

        foreach ($results as $result) {
            $status = match ($result['status']) {
                'success' => '<fg=green>✓ Berhasil</>',
                'failed' => '<fg=yellow>⚠ Tidak ditemukan</>',
                'error' => '<fg=red>✗ Error</>',
            };

            $price = $result['price'] !== null
                ? 'Rp ' . number_format($result['price'], 0, ',', '.')
                : '-';

            $tableData[] = [
                $result['ticker'],
                $price,
                $status,
            ];
        }

        $this->table(
            ['Kode Saham', 'Harga', 'Status'],
            $tableData
        );
    }
}
