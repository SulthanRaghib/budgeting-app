<?php

namespace App\Observers;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountObserver
{
    /**
     * Handle the Account "creating" event.
     */
    public function creating(Account $account): void
    {
        // Ensure the account is associated with the current user when created via forms
        if (empty($account->user_id)) {
            $account->user_id = Auth::id();
        }

        // If current_balance not provided, initialise it to initial_balance
        if ($account->current_balance === null) {
            $account->current_balance = $account->initial_balance ?? 0;
        }
    }

    /**
     * Handle the Account "updating" event.
     */
    public function updating(Account $account): void
    {
        if ($account->isDirty('initial_balance')) {
            $newInitial = (float) ($account->initial_balance ?? 0);

            $income = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.account_id', $account->id)
                ->where('categories.type', 'income')
                ->sum('transactions.amount');

            $expense = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.account_id', $account->id)
                ->where('categories.type', 'expense')
                ->sum('transactions.amount');

            $net = (float) $income - (float) $expense;

            $account->current_balance = $newInitial + $net;
        }
    }
}
