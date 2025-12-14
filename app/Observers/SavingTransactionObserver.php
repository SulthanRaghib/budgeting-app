<?php

namespace App\Observers;

use App\Models\SavingTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SavingTransactionObserver
{
    public function creating(SavingTransaction $transaction): void
    {
        // Ensure the transaction belongs to the authenticated user
        if (! $transaction->user_id) {
            $transaction->user_id = Auth::id();
        }
    }

    public function created(SavingTransaction $transaction): void
    {
        // Update related saving goal atomically
        DB::transaction(function () use ($transaction) {
            $goal = $transaction->savingGoal()->lockForUpdate()->first();

            if (! $goal) {
                return;
            }

            // Increment current_amount
            $goal->increment('current_amount', $transaction->amount);

            // Refresh to get latest value
            $goal->refresh();

            // If goal reached or exceeded, mark as completed
            if ($goal->current_amount >= $goal->target_amount && $goal->status !== 'completed') {
                $goal->status = 'completed';
                $goal->save();
            }
        });
    }
}
