<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class YahooFinanceService
{
    /**
     * Yahoo Finance API base URL
     */
    private const API_URL = 'https://query1.finance.yahoo.com/v8/finance/chart/';

    /**
     * User-Agent header to avoid 403 errors
     */
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Fetch stock data from Yahoo Finance
     *
     * @param string $ticker Stock ticker (without .JK suffix)
     * @return array|null Returns array with price data or null if failed
     */
    public function fetchStockData(string $ticker): ?array
    {
        $ticker = strtoupper(trim($ticker));
        $yahooTicker = $ticker . '.JK';
        $cacheKey = "yahoo_stock_{$ticker}";

        // Check cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US,en;q=0.9',
            ])
                ->timeout(15)
                ->get(self::API_URL . $yahooTicker, [
                    'interval' => '1d',
                    'range' => '1d',
                ]);

            if (!$response->successful()) {
                Log::warning("Yahoo Finance API error for {$ticker}", [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            // Check if we have valid result
            if (!isset($data['chart']['result'][0]['meta'])) {
                Log::warning("Invalid response structure for {$ticker}");
                return null;
            }

            $meta = $data['chart']['result'][0]['meta'];

            $result = [
                'ticker' => $ticker,
                'symbol' => $meta['symbol'] ?? $yahooTicker,
                'name' => $meta['longName'] ?? $meta['shortName'] ?? $ticker,
                'currency' => $meta['currency'] ?? 'IDR',
                'exchange' => $meta['fullExchangeName'] ?? 'Jakarta',
                'current_price' => $meta['regularMarketPrice'] ?? null,
                'previous_close' => $meta['previousClose'] ?? null,
                'day_high' => $meta['regularMarketDayHigh'] ?? null,
                'day_low' => $meta['regularMarketDayLow'] ?? null,
                'volume' => $meta['regularMarketVolume'] ?? null,
                'fifty_two_week_high' => $meta['fiftyTwoWeekHigh'] ?? null,
                'fifty_two_week_low' => $meta['fiftyTwoWeekLow'] ?? null,
                'last_updated' => now()->toDateTimeString(),
            ];

            // Cache the result
            Cache::put($cacheKey, $result, self::CACHE_TTL);

            return $result;
        } catch (\Exception $e) {
            Log::error("Failed to fetch stock data for {$ticker}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get only the current price for a ticker
     *
     * @param string $ticker Stock ticker (without .JK suffix)
     * @return float|null
     */
    public function getCurrentPrice(string $ticker): ?float
    {
        $data = $this->fetchStockData($ticker);
        return $data['current_price'] ?? null;
    }

    /**
     * Check if a ticker exists in Yahoo Finance
     *
     * @param string $ticker Stock ticker (without .JK suffix)
     * @return bool
     */
    public function tickerExists(string $ticker): bool
    {
        $data = $this->fetchStockData($ticker);
        return $data !== null && isset($data['current_price']);
    }

    /**
     * Clear cache for a specific ticker
     *
     * @param string $ticker
     * @return void
     */
    public function clearCache(string $ticker): void
    {
        $ticker = strtoupper(trim($ticker));
        Cache::forget("yahoo_stock_{$ticker}");
    }
}
