<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockHolding extends Model
{
    /**
     * 1 Lot = 100 lembar saham
     */
    public const SHARES_PER_LOT = 100;

    protected $fillable = [
        'user_id',
        'ticker',
        'total_shares', // Stored as number of LOTS
        'average_price',
        'current_price',
    ];

    protected $casts = [
        'total_shares' => 'integer',
        'average_price' => 'decimal:2',
        'current_price' => 'decimal:2',
    ];

    protected $appends = ['unrealized_gain_loss', 'market_value', 'total_cost', 'total_lembar'];

    /**
     * Get total shares in lembar (lot × 100)
     */
    public function getTotalLembarAttribute(): int
    {
        return $this->total_shares * self::SHARES_PER_LOT;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Calculate unrealized gain/loss: (Current Price - Avg Price) × Total Lembar
     */
    public function getUnrealizedGainLossAttribute(): float
    {
        if (!$this->current_price || $this->total_shares <= 0) {
            return 0;
        }

        return ($this->current_price - $this->average_price) * $this->total_lembar;
    }

    /**
     * Calculate market value: Current Price × Total Lembar (lot × 100)
     */
    public function getMarketValueAttribute(): float
    {
        if (!$this->current_price) {
            return 0;
        }

        return $this->current_price * $this->total_lembar;
    }

    /**
     * Calculate total cost: Average Price × Total Lembar (lot × 100)
     */
    public function getTotalCostAttribute(): float
    {
        return $this->average_price * $this->total_lembar;
    }

    /**
     * Recalculate weighted average price after a buy transaction.
     * Formula: ((Old Avg × Old Lot) + (New Price × New Lot)) / Total Lot
     * Note: newShares parameter is in LOT (not lembar)
     */
    public function recalculateAveragePrice(int $newShares, float $newPrice): void
    {
        $oldTotalCost = $this->average_price * $this->total_shares;
        $newTotalCost = $newPrice * $newShares;
        $newTotalShares = $this->total_shares + $newShares;

        if ($newTotalShares > 0) {
            $this->average_price = ($oldTotalCost + $newTotalCost) / $newTotalShares;
        }

        $this->total_shares = $newTotalShares;
        $this->save();
    }

    /**
     * Decrease shares on sell.
     * Note: Average price remains unchanged on sell.
     * @param int $shares Number of LOT to decrease (not lembar)
     */
    public function decreaseShares(int $shares): void
    {
        $this->total_shares = max(0, $this->total_shares - $shares);
        $this->save();
    }
}
