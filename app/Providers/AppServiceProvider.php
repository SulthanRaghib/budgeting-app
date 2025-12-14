<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SavingTransaction;
use App\Observers\SavingTransactionObserver;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use App\Models\Account;
use App\Observers\AccountObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SavingTransaction::observe(SavingTransactionObserver::class);
        Transaction::observe(TransactionObserver::class);
        Account::observe(AccountObserver::class);
    }
}
