<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $this->applyToAccount($transaction, +1);
    }

    public function deleted(Transaction $transaction): void
    {
        $this->applyToAccount($transaction, -1);
    }

    public function updated(Transaction $transaction): void
    {
        // Handle cases where account, amount, or category type changed.
        $original = $transaction->getOriginal();

        $oldAccountId = $original['account_id'] ?? null;
        $newAccountId = $transaction->account_id;

        $oldAmount = $original['amount'] ?? 0;
        $newAmount = $transaction->amount;

        $oldCategoryId = $original['category_id'] ?? null;
        $newCategoryId = $transaction->category_id;

        // If account changed, revert old and apply to new
        if ($oldAccountId && $oldAccountId !== $newAccountId) {
            $old = $transaction->replicate();
            $old->account_id = $oldAccountId;
            $old->amount = $oldAmount;
            $old->category_id = $oldCategoryId;
            $this->applyToAccount($old, -1);
            $this->applyToAccount($transaction, +1);
            return;
        }

        // If amount or category type changed, compute delta
        if ($oldAmount != $newAmount || $oldCategoryId != $newCategoryId) {
            // Revert old
            $old = $transaction->replicate();
            $old->amount = $oldAmount;
            $old->category_id = $oldCategoryId;
            $this->applyToAccount($old, -1);

            // Apply new
            $this->applyToAccount($transaction, +1);
        }
    }

    protected function applyToAccount(Transaction $transaction, int $direction): void
    {
        // direction: +1 => apply, -1 => revert
        if (! $transaction->account) {
            return;
        }

        $account = $transaction->account;

        $amount = (float) $transaction->amount;
        $isIncome = optional($transaction->category)->type === 'income';

        DB::transaction(function () use ($account, $amount, $isIncome, $direction) {
            if ($isIncome) {
                $account->increment('current_balance', $direction * $amount);
            } else {
                $account->decrement('current_balance', $direction * $amount);
            }
        });
    }
}
