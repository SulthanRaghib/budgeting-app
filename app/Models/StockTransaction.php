<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'stock_holding_id',
        'account_id',
        'type',
        'shares',
        'price',
        'fee',
        'date',
        'notes',
    ];

    protected $casts = [
        'shares' => 'integer',
        'price' => 'decimal:2',
        'fee' => 'decimal:2',
        'date' => 'date',
    ];

    protected $appends = ['total_amount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockHolding(): BelongsTo
    {
        return $this->belongsTo(StockHolding::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Calculate total transaction amount including fee.
     * Buy: (shares × 100 × price) + fee
     * Sell: (shares × 100 × price) - fee
     * Dividend: price (amount received)
     * Note: shares is stored as LOT, so multiply by 100 to get lembar
     */
    public function getTotalAmountAttribute(): float
    {
        $lembar = $this->shares * StockHolding::SHARES_PER_LOT;
        $baseAmount = $lembar * $this->price;

        return match ($this->type) {
            'buy' => $baseAmount + $this->fee,
            'sell' => $baseAmount - $this->fee,
            'dividend' => $this->price, // For dividend, price field stores the amount
            default => $baseAmount,
        };
    }
}
