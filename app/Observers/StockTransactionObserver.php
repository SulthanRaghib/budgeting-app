<?php

namespace App\Observers;

use App\Models\StockTransaction;
use App\Models\StockHolding;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionObserver
{
    /**
     * Handle the StockTransaction "creating" event.
     * Validates and prepares the transaction before saving.
     */
    public function creating(StockTransaction $stockTransaction): void
    {
        // Ensure user_id is set
        if (!$stockTransaction->user_id) {
            $stockTransaction->user_id = Auth::id();
        }

        // Validate sell transaction has sufficient shares
        if ($stockTransaction->type === 'sell') {
            $holding = StockHolding::find($stockTransaction->stock_holding_id);

            if (!$holding || $holding->total_shares < $stockTransaction->shares) {
                throw ValidationException::withMessages([
                    'shares' => 'Lot tidak cukup untuk dijual. Tersedia: ' . ($holding?->total_shares ?? 0) . ' lot',
                ]);
            }
        }

        // Validate buy/sell has sufficient account balance
        if ($stockTransaction->type === 'buy') {
            $account = $stockTransaction->account;
            // shares is in LOT, multiply by 100 for lembar
            $lembar = $stockTransaction->shares * StockHolding::SHARES_PER_LOT;
            $totalCost = ($lembar * $stockTransaction->price) + $stockTransaction->fee;

            if ($account && $account->current_balance < $totalCost) {
                throw ValidationException::withMessages([
                    'account_id' => 'Saldo tidak cukup. Dibutuhkan: Rp ' . number_format($totalCost, 0, ',', '.'),
                ]);
            }
        }
    }

    /**
     * Handle the StockTransaction "created" event.
     */
    public function created(StockTransaction $stockTransaction): void
    {
        DB::transaction(function () use ($stockTransaction) {
            match ($stockTransaction->type) {
                'buy' => $this->handleBuy($stockTransaction),
                'sell' => $this->handleSell($stockTransaction),
                'dividend' => $this->handleDividend($stockTransaction),
            };
        });
    }

    /**
     * Handle BUY transaction:
     * - Deduct (lembar × price) + fee from Account balance
     * - Recalculate StockHolding weighted average price and increase shares (lot)
     */
    private function handleBuy(StockTransaction $stockTransaction): void
    {
        // 1. Deduct from account balance (shares is LOT, convert to lembar)
        $lembar = $stockTransaction->shares * StockHolding::SHARES_PER_LOT;
        $totalCost = ($lembar * $stockTransaction->price) + $stockTransaction->fee;
        $account = $stockTransaction->account;

        if ($account) {
            $account->current_balance -= $totalCost;
            $account->save();
        }

        // 2. Update or create stock holding (shares stored as LOT)
        $holding = StockHolding::find($stockTransaction->stock_holding_id);

        if ($holding) {
            $holding->recalculateAveragePrice($stockTransaction->shares, $stockTransaction->price);
        }
    }

    /**
     * Handle SELL transaction:
     * - Add (lembar × price) - fee to Account balance
     * - Decrease StockHolding shares (lot)
     */
    private function handleSell(StockTransaction $stockTransaction): void
    {
        // 1. Add to account balance (shares is LOT, convert to lembar)
        $lembar = $stockTransaction->shares * StockHolding::SHARES_PER_LOT;
        $totalProceeds = ($lembar * $stockTransaction->price) - $stockTransaction->fee;
        $account = $stockTransaction->account;

        if ($account) {
            $account->current_balance += $totalProceeds;
            $account->save();
        }

        // 2. Decrease holding shares (stored as LOT)
        $holding = StockHolding::find($stockTransaction->stock_holding_id);

        if ($holding) {
            $holding->decreaseShares($stockTransaction->shares);
        }
    }

    /**
     * Handle DIVIDEND transaction:
     * - Add dividend amount to Account balance
     */
    private function handleDividend(StockTransaction $stockTransaction): void
    {
        $account = $stockTransaction->account;

        if ($account) {
            // For dividend, the 'price' field stores the total dividend amount
            $account->current_balance += $stockTransaction->price;
            $account->save();
        }
    }

    /**
     * Handle the StockTransaction "deleted" event.
     * Reverses the transaction effects.
     */
    public function deleted(StockTransaction $stockTransaction): void
    {
        DB::transaction(function () use ($stockTransaction) {
            match ($stockTransaction->type) {
                'buy' => $this->reverseBuy($stockTransaction),
                'sell' => $this->reverseSell($stockTransaction),
                'dividend' => $this->reverseDividend($stockTransaction),
            };
        });
    }

    /**
     * Reverse BUY: Add back to account, recalculate holding
     */
    private function reverseBuy(StockTransaction $stockTransaction): void
    {
        // Add back to account (shares is LOT, convert to lembar)
        $lembar = $stockTransaction->shares * StockHolding::SHARES_PER_LOT;
        $totalCost = ($lembar * $stockTransaction->price) + $stockTransaction->fee;
        $account = $stockTransaction->account;

        if ($account) {
            $account->current_balance += $totalCost;
            $account->save();
        }

        // Decrease shares from holding
        $holding = StockHolding::find($stockTransaction->stock_holding_id);

        if ($holding) {
            $holding->decreaseShares($stockTransaction->shares);

            // Recalculate average price based on remaining transactions
            $this->recalculateHoldingFromTransactions($holding);
        }
    }

    /**
     * Reverse SELL: Deduct from account, add back shares
     */
    private function reverseSell(StockTransaction $stockTransaction): void
    {
        // Deduct from account (shares is LOT, convert to lembar)
        $lembar = $stockTransaction->shares * StockHolding::SHARES_PER_LOT;
        $totalProceeds = ($lembar * $stockTransaction->price) - $stockTransaction->fee;
        $account = $stockTransaction->account;

        if ($account) {
            $account->current_balance -= $totalProceeds;
            $account->save();
        }

        // Add back shares to holding
        $holding = StockHolding::find($stockTransaction->stock_holding_id);

        if ($holding) {
            $holding->total_shares += $stockTransaction->shares;
            $holding->save();
        }
    }

    /**
     * Reverse DIVIDEND: Deduct from account
     */
    private function reverseDividend(StockTransaction $stockTransaction): void
    {
        $account = $stockTransaction->account;

        if ($account) {
            $account->current_balance -= $stockTransaction->price;
            $account->save();
        }
    }

    /**
     * Recalculate holding average price from all remaining buy transactions
     */
    private function recalculateHoldingFromTransactions(StockHolding $holding): void
    {
        $buyTransactions = $holding->stockTransactions()
            ->where('type', 'buy')
            ->get();

        if ($buyTransactions->isEmpty()) {
            $holding->average_price = 0;
            $holding->save();
            return;
        }

        $totalCost = 0;
        $totalShares = 0;

        foreach ($buyTransactions as $transaction) {
            $totalCost += $transaction->shares * $transaction->price;
            $totalShares += $transaction->shares;
        }

        // Account for sold shares
        $soldShares = $holding->stockTransactions()
            ->where('type', 'sell')
            ->sum('shares');

        $remainingShares = $totalShares - $soldShares;

        if ($remainingShares > 0) {
            $holding->average_price = $totalCost / $totalShares;
            $holding->total_shares = $remainingShares;
        } else {
            $holding->average_price = 0;
            $holding->total_shares = 0;
        }

        $holding->save();
    }
}
