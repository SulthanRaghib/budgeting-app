<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Transfer extends Model
{
    protected $fillable = [
        'user_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    protected static function booted()
    {
        // Ensure that source and destination accounts are different
        static::saving(function (Transfer $transfer) {
            if ($transfer->from_account_id === $transfer->to_account_id) {
                throw ValidationException::withMessages([
                    'to_account_id' => ['Destination account must be different from source account.'],
                ]);
            }
        });
    }
}
